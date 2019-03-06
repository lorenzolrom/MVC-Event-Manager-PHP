<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:44 PM
 */


namespace controllers;


use database\SessionDatabaseHandler;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\RouteException;
use exceptions\SecurityException;
use exceptions\ValidationUtility;
use factories\AttendeeFactory;
use factories\EventFactory;
use factories\RoleFactory;
use factories\SessionFactory;
use factories\VenueFactory;
use models\Attendee;
use models\Event;
use models\Session;
use models\Venue;
use views\AdminPage;
use views\CreateAttendeePage;
use views\CreateVenuePage;
use views\EditAttendeePage;
use views\EditVenuePage;
use views\EventAdminPage;
use views\EventCreatePage;
use views\EventEditAdminPage;
use views\SessionAddAttendeePage;
use views\SessionAdminPage;
use views\SessionCreatePage;
use views\SessionEditPage;

class AdminController extends Controller
{
    /**
     * @param string $uri
     * @return string
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\SecurityException
     * @throws \exceptions\ViewException
     * @throws RouteException
     */
    public function getPage(string $uri): string
    {
        FrontController::validateRole(['admin', 'event manager']);

        $uriParts = explode("/", $uri);
        array_shift($uriParts); // Remove "admin"
        $secondPart = array_shift($uriParts); // Get subject

        if($uri == "admin")
        {
            $adminPage = new AdminPage();
            return $adminPage->getHTML();
        }
        else if($secondPart == "attendees")
        {
            if(sizeof($uriParts) == 2 AND $uriParts[0] == "edit") // EDIT USER
            {
                FrontController::validateRole(['admin']);
                $attendee = NULL;

                try
                {
                    $attendee = AttendeeFactory::getById(intval($uriParts[1]));
                }
                catch(EntryNotFoundException $e)
                {
                    // Do nothing, will be caught by edit page
                }

                $editPage = new EditAttendeePage(intval($uriParts[1]));

                if(!empty($_POST))
                {
                    $errors = array();

                    // Validate name
                    switch(Attendee::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                        case ValidationUtility::VALUE_ALREADY_TAKEN:
                            if($_POST['name'] != $attendee->getName()) // Allow the same name to be used
                                $errors[] = "Name Already Taken";
                    }

                    if(isset($_POST['password']) AND strlen($_POST['password']) != 0) // Password set is optional here
                    {
                        // Validate password
                        switch(Attendee::validatePassword(ValidationUtility::ifNull($_POST['password'])))
                        {
                            case ValidationUtility::VALUE_IS_NULL:
                                $errors[] = "Password Required";
                                break;
                            case ValidationUtility::VALUE_TOO_SHORT:
                                $errors[] = "Password Must Be At Least 8 Characters";
                                break;
                            default:
                                if(ValidationUtility::ifNull($_POST['password'] != ValidationUtility::ifNull($_POST['confirm'])))
                                    $errors[] = "Passwords Do Not Match";
                                break;
                        }
                    }

                    // Validate role
                    switch(Attendee::validateRole(ValidationUtility::ifNull(intval($_POST['roles']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Role Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Role Is Invalid";
                            break;
                    }

                    if(empty($errors))
                    {
                        $attendee->setName($_POST['name']);
                        $attendee->setRole($_POST['roles']);

                        if(isset($_POST['password']) AND strlen($_POST['password']) != 0)
                            $attendee->setPassword($_POST['password']);

                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Attendee Updated");
                        exit;
                    }
                    else
                    {
                        $editPage->setErrors($errors);
                    }
                }

                return $editPage->getHTML();
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "delete") // DELETE USER
            {
                FrontController::validateRole(['admin']);

                try
                {
                    $attendee = AttendeeFactory::getById(intval($uriParts[1]));
                    if($attendee->delete())
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Attendee Deleted");
                    else
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?ERROR=Could Not Delete Attendee");
                    exit;
                }
                catch(EntryNotFoundException $e)
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "admin?ERROR=Attendee Not Found");
                    exit;
                }
            }
            else if(sizeof($uriParts) == 1 AND $uriParts[0] == "create") // CREATE NEW USER
            {
                FrontController::validateRole(['admin']);

                $createPage = new CreateAttendeePage();

                // Process create request
                if(!empty($_POST))
                {
                    $errors = array();

                    // Validate name
                    switch(Attendee::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                        case ValidationUtility::VALUE_ALREADY_TAKEN:
                            $errors[] = "Name Already Taken";
                    }

                    // Validate password
                    switch(Attendee::validatePassword(ValidationUtility::ifNull($_POST['password'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Password Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                            $errors[] = "Password Must Be At Least 8 Characters";
                            break;
                        default:
                            if(ValidationUtility::ifNull($_POST['password'] != ValidationUtility::ifNull($_POST['confirm'])))
                                $errors[] = "Passwords Do Not Match";
                            break;
                    }

                    // Validate role
                    switch(Attendee::validateRole(ValidationUtility::ifNull(intval($_POST['roles']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Role Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Role Is Invalid";
                            break;
                    }

                    // Create attendee
                    if(empty($errors))
                    {
                        AttendeeFactory::getNew($_POST['name'], $_POST['password'], $_POST['roles']);
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Attendee Created");
                        exit;
                    }
                    else // Display errors
                    {
                        $createPage->setErrors($errors);
                    }

                }

                return $createPage->getHTML();
            }
        }
        else if($secondPart == "venues")
        {
            if(sizeof($uriParts) == 2 AND $uriParts[0] == "edit") // EDIT VENUE
            {
                FrontController::validateRole(['admin']);
                $venue = NULL;

                try
                {
                    $venue = VenueFactory::getById(intval($uriParts[1]));
                }
                catch(EntryNotFoundException $e)
                {
                    // Do nothing, will be caught by edit page
                }

                $editPage = new EditVenuePage(intval($uriParts[1]));

                if(!empty($_POST))
                {
                    $errors = array();

                    // Validate name
                    switch(Venue::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                    }

                    // Validate capacity
                    switch(Venue::validateCapacity(intval(ValidationUtility::ifNull($_POST['capacity']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Capacity Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Capacity Is Invalid";
                            break;

                    }

                    // Update venue
                    if(empty($errors))
                    {
                        $venue->setName($_POST['name']);
                        $venue->setCapacity($_POST['capacity']);
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Venue Updated");
                        exit;
                    }
                    else // Display errors
                    {
                        $editPage->setErrors($errors);
                    }
                }

                return $editPage->getHTML();
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "delete") // DELETE VENUE
            {
                FrontController::validateRole(['admin']);

                try
                {
                    $venue = VenueFactory::getById(intval($uriParts[1]));
                    if($venue->delete())
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Venue Deleted");
                    else
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?ERROR=Could Not Delete Venue");
                    exit;
                }
                catch(EntryNotFoundException $e)
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "admin?ERROR=Venue Not Found");
                    exit;
                }
            }
            else if(sizeof($uriParts) == 1 AND $uriParts[0] == "create") // CREATE VENUE
            {
                FrontController::validateRole(['admin']);

                $createPage = new CreateVenuePage();

                if(!empty($_POST))
                {
                    $errors = array();

                    // Validate name
                    switch(Venue::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                    }

                    // Validate capacity
                    switch(Venue::validateCapacity(intval(ValidationUtility::ifNull($_POST['capacity']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Capacity Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Capacity Is Invalid";
                            break;

                    }

                    // Update venue
                    if(empty($errors))
                    {
                        VenueFactory::getNew($_POST['name'], $_POST['capacity']);
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Venue Created");
                        exit;
                    }
                    else // Display errors
                    {
                        $createPage->setErrors($errors);
                    }
                }

                return $createPage->getHTML();
            }
        }
        else if($secondPart == "events")
        {
            if(sizeof($uriParts) == 2 AND $uriParts[0] == "view") // View Event
            {
                try
                {
                    // Check if user is a manager and can see this event
                   FrontController::validateRole(['event manager']);
                   if(!FrontController::getCurrentUser()->isAManager(intval($uriParts[1])))
                       throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    // Check if user is an admin
                    FrontController::validateRole(['admin']);
                }

                $viewPage = new EventAdminPage(intval($uriParts[1]));
                return $viewPage->getHTML();
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "edit") // Edit event
            {
                try
                {
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager(intval($uriParts[1])))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    // Check if user is an admin
                    FrontController::validateRole(['admin']);
                }

                $event = NULL;

                try
                {
                    $event = EventFactory::getById(intval($uriParts[1]));
                }
                catch(EntryNotFoundException $e)
                {
                    // Will be caught by edit page
                }

                $editPage = new EventEditAdminPage(intval($uriParts[1]));

                if(!empty($_POST))
                {
                    $errors = [];

                    // Validate name
                    switch(Event::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_ALREADY_TAKEN:
                            if($_POST['name'] != $event->getName())
                                $errors[] = "Name Already Taken";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                    }

                    // Validate number allowed
                    switch(Event::validateNumberAllowed(intval(ValidationUtility::ifNull($_POST['numberallowed']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Maximum Attendees Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Maximum Attendees Is Invalid";
                            break;
                    }

                    // Validate start date
                    switch(Event::validateDateX(ValidationUtility::ifNull($_POST['datestart'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Begin Date Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Begin Date Is Invalid";
                            break;
                    }

                    // Validate end date
                    switch(Event::validateDateX(ValidationUtility::ifNull($_POST['dateend'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "End Date Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "End Date Is Invalid";
                            break;
                    }

                    // Validate venue
                    switch(Event::validateVenue(ValidationUtility::ifNull($_POST['venue'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Venue Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Venue Is Invalid";
                            break;
                    }

                    // Validate managers
                    switch(Event::validateManagers(isset($_POST['managers']) ? $_POST['managers'] : NULL))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $_POST['managers'] = array();
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "One or More Managers Not Found";
                            break;
                    }

                    if(empty($errors))
                    {
                        $event->setName($_POST['name']);
                        $event->setDateend($_POST['dateend']);
                        $event->setDatestart($_POST['datestart']);
                        $event->setNumberallowed(intval($_POST['numberallowed']));
                        $event->setVenue(intval($_POST['venue']));
                        $event->setManagers($_POST['managers']);

                        header("Location: " . FB_CONFIG['baseURI'] . "admin/events/view/" . $event->getIdevent() . "?NOTICE=Event Updated");
                        exit;
                    }
                    else
                    {
                        $editPage->setErrors($errors);
                    }
                }

                return $editPage->getHTML();
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "delete")
            {
                try
                {
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager(intval($uriParts[1])))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    // Check if user is an admin
                    FrontController::validateRole(['admin']);
                }

                try
                {
                    $event = EventFactory::getById(intval($uriParts[1]));
                    if($event->delete())
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Event Deleted");
                    else
                        header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Could Not Delete Event");
                    exit;
                }
                catch(EntryNotFoundException $e)
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "admin?NOTICE=Event Not Found");
                    exit;
                }
            }
            else if(sizeof($uriParts) == 1 AND $uriParts[0] == "create")
            {
                // Check if user is a manager and can see this event
                FrontController::validateRole(['event manager', 'admin']);

                $createPage = new EventCreatePage();

                if(!empty($_POST))
                {
                    $errors = [];

                    // Validate name
                    switch(Event::validateName(ValidationUtility::ifNull($_POST['name'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_ALREADY_TAKEN:
                            $errors[] = "Name Already Taken";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 And 50 Characters";
                            break;
                    }

                    // Validate number allowed
                    switch(Event::validateNumberAllowed(intval(ValidationUtility::ifNull($_POST['numberallowed']))))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Maximum Attendees Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Maximum Attendees Is Invalid";
                            break;
                    }

                    // Validate start date
                    switch(Event::validateDateX(ValidationUtility::ifNull($_POST['datestart'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Begin Date Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Begin Date Is Invalid";
                            break;
                    }

                    // Validate end date
                    switch(Event::validateDateX(ValidationUtility::ifNull($_POST['dateend'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "End Date Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "End Date Is Invalid";
                            break;
                    }

                    // Validate venue
                    switch(Event::validateVenue(ValidationUtility::ifNull($_POST['venue'])))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Venue Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Venue Is Invalid";
                            break;
                    }

                    // Validate managers
                    switch(Event::validateManagers(isset($_POST['managers']) ? $_POST['managers'] : NULL))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $_POST['managers'] = array();
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "One or More Managers Not Found";
                            break;
                    }

                    if(empty($errors))
                    {
                        $event = EventFactory::getNew($_POST['name'], $_POST['datestart'], $_POST['dateend'], intval($_POST['numberallowed']), intval($_POST['venue']));
                        $event->setManagers($_POST['managers']);


                        header("Location: " . FB_CONFIG['baseURI'] . "admin/events/view/" . $event->getIdevent() . "?NOTICE=Event Created");
                        exit;
                    }
                    else
                    {
                        $createPage->setErrors($errors);
                    }
                }

                return $createPage->getHTML();
            }
            else if(sizeof($uriParts) == 3 AND $uriParts[1] == "sessions" AND $uriParts[2] == "create") // Create session for event
            {
                $event = NULL;

                try
                {
                    $event = EventFactory::getById(intval($uriParts[0]));
                }
                catch(EntryNotFoundException $e)
                {
                    // Ignore, will be caught by create page
                }

                $createPage = new SessionCreatePage(intval($uriParts[0]));

                if(!empty($_POST))
                {
                    $errors = array();
                    // Validate name
                    switch(Session::validateName(isset($_POST['name']) ? $_POST['name'] : NULL))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Session Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Session Name Must Be Between 1 And 50 Characters";
                            break;
                    }

                    // Validate numberallowed
                    switch(Session::validateNumberAllowed(isset($_POST['numberallowed']) ? intval($_POST['numberallowed']) : NULL))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Maximum Attendees Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Maximum Attendees Is Invalid";
                            break;
                    }

                    // Validate start date + time
                    $startDate = "";
                    $startDate .= isset($_POST['startdate']) ? $_POST['startdate'] : "";
                    $startDate .= isset($_POST['starttime']) ? (" " . $_POST['starttime']) : "";

                    switch(Session::validateXDate($startDate))
                    {
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Start Date/Time is Invalid";
                            break;
                    }

                    // Validate end date + time
                    $endDate = "";
                    $endDate .= isset($_POST['enddate']) ? $_POST['enddate'] : "";
                    $endDate .= isset($_POST['endtime']) ? (" " . $_POST['endtime']) : "";

                    switch(Session::validateXDate($endDate))
                    {
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "End Date/Time is Invalid";
                            break;
                    }

                    if(empty($errors))
                    {
                        $session = SessionFactory::getNew($_POST['name'], intval($_POST['numberallowed']), $event->getIdevent(), $startDate, $endDate);
                        header("Location: " . FB_CONFIG['baseURI'] . "admin/sessions/view/" . $session->getIdsession());
                        exit;
                    }
                    else
                    {
                        $createPage->setErrors($errors);
                    }
                }

                return $createPage->getHTML();
            }
        }
        else if($secondPart == "sessions")
        {
            if(sizeof($uriParts) == 2 AND $uriParts[0] == "view") // View Session
            {
                try
                {
                    $event = EventFactory::getById(SessionFactory::getById(intval($uriParts[1]))->getEvent());
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    FrontController::validateRole(['admin']);
                }
                catch(EntryNotFoundException $e)
                {
                    // Will be handled by page
                }

                $viewPage = new SessionAdminPage(intval($uriParts[1]));
                return $viewPage->getHTML();
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "delete")
            {
                $event = NULL;
                try
                {
                    $event = EventFactory::getById(SessionFactory::getById(intval($uriParts[1]))->getEvent());
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    FrontController::validateRole(['admin']);
                }
                catch(EntryNotFoundException $e)
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "admin?ERROR=Session Not Found");
                    exit;
                }

                $session = SessionFactory::getById(intval($uriParts[1]));

                if($session->delete())
                    header("Location: " . FB_CONFIG['baseURI'] . "admin/events/view/" . $event->getIdevent() . "?NOTICE=Session Deleted");
                else
                    header("Location: " . FB_CONFIG['baseURI'] . "admin/events/view/" . $event->getIdevent() . "?ERROR=Could Not Delete Session");
                exit;
            }
            else if(sizeof($uriParts) == 2 AND $uriParts[0] == "edit") // edit session
            {
                $session = NULL;
                try
                {
                    $session = SessionFactory::getById(intval($uriParts[1]));
                    $event = EventFactory::getById($session->getEvent());
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    FrontController::validateRole(['admin']);
                }
                catch(EntryNotFoundException $e)
                {
                    // Will be handled by page
                }

                $editPage = new SessionEditPage(intval($uriParts[1]));

                // process submission
                if(empty(!$_POST))
                {
                    // Validate name
                    switch(Session::validateName(isset($_POST['name']) ? $_POST['name'] : NULL))
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Name Required";
                            break;
                        case ValidationUtility::VALUE_TOO_SHORT:
                        case ValidationUtility::VALUE_TOO_LONG:
                            $errors[] = "Name Must Be Between 1 and 50 Characters";
                            break;
                    }

                    // Validate numberallowed
                    switch(Session::validateNumberAllowed(isset($_POST['numberallowed'])) ? $_POST['numberallowed'] : NULL)
                    {
                        case ValidationUtility::VALUE_IS_NULL:
                            $errors[] = "Maximum Attendees Required";
                            break;
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Maximum Attendees Is Invalid";
                            break;
                    }

                    // Validate start date + time
                    $startDate = "";
                    $startDate .= isset($_POST['startdate']) ? $_POST['startdate'] : "";
                    $startDate .= isset($_POST['starttime']) ? (" " . $_POST['starttime']) : "";

                    switch(Session::validateXDate($startDate))
                    {
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "Start Date/Time is Invalid";
                            break;
                    }

                    // Validate end date + time
                    $endDate = "";
                    $endDate .= isset($_POST['enddate']) ? $_POST['enddate'] : "";
                    $endDate .= isset($_POST['endtime']) ? (" " . $_POST['endtime']) : "";

                    switch(Session::validateXDate($endDate))
                    {
                        case ValidationUtility::VALUE_IS_INVALID:
                            $errors[] = "End Date/Time is Invalid";
                            break;
                    }

                    if(empty($errors))
                    {
                        $session->setName($_POST['name']);
                        $session->setNumberallowed(intval($_POST['numberallowed']));
                        $session->setStartdate($startDate);
                        $session->setEnddate($endDate);

                        header("Location: " . FB_CONFIG['baseURI'] . "admin/sessions/view/{$session->getIdsession()}?NOTICE=Session Updated");
                        exit;
                    }
                    else
                    {
                        $editPage->setErrors($errors);
                    }
                }

                return $editPage->getHTML();
            }
            else if(sizeof($uriParts) == 3 AND $uriParts[1] == "unregister") // Remove attendee from session
            {
                $session = NULL;
                try
                {
                    $session = SessionFactory::getById(intval($uriParts[1]));
                    $event = EventFactory::getById($session->getEvent());
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    FrontController::validateRole(['admin']);
                }
                catch(EntryNotFoundException $e)
                {
                    // Will be handled by page
                }

                try
                {
                    $session = SessionFactory::getById(intval($uriParts[0]));
                    $attendee = AttendeeFactory::getById(intval($uriParts[2]));

                    $attendee->unregisterFromSession($session->getIdsession());
                    header("Location: " . FB_CONFIG['baseURI'] . "admin/sessions/view/" . intval($uriParts[0]) . "?NOTICE=Attendee Removed From Session");
                    exit;
                }
                catch(EntryNotFoundException $e)
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "admin/sessions/view/" . intval($uriParts[0]) . "?ERROR=Session or Attendee Not Found");
                    exit;
                }
            }
            else if(sizeof($uriParts) == 3 AND $uriParts[2] == "register")
            {
                $session = NULL;
                try
                {
                    $session = SessionFactory::getById(intval($uriParts[1]));
                    $event = EventFactory::getById($session->getEvent());
                    // Check if user is a manager and can see this event
                    FrontController::validateRole(['event manager']);
                    if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                        throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
                catch(SecurityException $e)
                {
                    FrontController::validateRole(['admin']);
                }
                catch(EntryNotFoundException $e)
                {
                    // Will be handled by page
                }

                $registerPage = new SessionAddAttendeePage(intval($uriParts[1]));

                if(!empty($_POST))
                {
                    // Validate name

                    try
                    {
                        $attendee = AttendeeFactory::getByName(isset($_POST['name']) ? $_POST['name'] : "");
                        $attendee->registerForSession(intval($uriParts[1]));

                        header("Location: " . FB_CONFIG['baseURI'] . "admin/sessions/view/" . intval($uriParts[1]) . "?NOTICE=Attendee Added");
                        exit;
                    }
                    catch(EntryNotFoundException $e)
                    {
                        $registerPage->setErrors(['Attendee Not Found']);
                    }
                }

                return $registerPage->getHTML();
            }
        }

        throw new RouteException(ErrorMessages::PAGE_NOT_FOUND . ": $uri", RouteException::ROUTE_URI_NOT_FOUND);
    }
}