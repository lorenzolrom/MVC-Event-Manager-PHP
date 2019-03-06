<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 8:47 PM
 */


namespace views;


use controllers\FrontController;
use exceptions\EntryNotFoundException;
use factories\AttendeeFactory;
use factories\EventFactory;
use factories\SessionFactory;
use factories\VenueFactory;

class EventAdminPage extends View
{
    /**
     * EventViewAdminPage constructor.
     * @param int $idevent
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(int $idevent)
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        try
        {
            $event = EventFactory::getById($idevent);
            parent::setVariable("pageTitle", "Event: " . $event->getName());
            parent::setVariable("content", self::templateFileContents("Event"));

            // Add menu
            parent::setVariable("eventMenu", "
                <div class='button-bar'>
                    <a class='button ' href='{{@baseURI}}admin/events/edit/" . $event->getIdevent() . "'>Edit</a>
                    <a class='button delete-button confirm-button' href='{{@baseURI}}admin/events/delete/" . $event->getIdevent() . "'>Delete</a>
                    <a class='button' href='{{@baseURI}}admin/events/" . $event->getIdevent() . "/sessions/create'>Create Session</a>
                </div>
            ");

            // Fill in event details
            $venue = VenueFactory::getById($event->getVenue());

            parent::setVariable("idevent", $event->getIdevent());
            parent::setVariable("name", $event->getName());
            parent::setVariable("datestart", FrontController::convertToDisplayDate($event->getDatestart(), 'Y-m-d'));
            parent::setVariable("dateend", FrontController::convertToDisplayDate($event->getDateend(), 'Y-m-d'));
            parent::setVariable("venueName", $venue->getName());
            parent::setVariable("numberallowed", $event->getNumberallowed());

            // Get event sessions
            $sessions = SessionFactory::getAllEvent($event->getIdevent());
            if(empty($sessions))
                parent::setVariable("sessionList", "<span class='red-message'>NO DATA FOUND</span>");
            else
            {
                $sessionTable = "<table class='results'>\n<tbody>\n<tr><th>Name</th><th>Time Start</th><th>Time End</th><th>Attendance Status</th></tr>\n";

                foreach($sessions as $session)
                {
                    $sessionTable .= "<tr>\n
                                           <td><a href='{{@baseURI}}admin/sessions/view/" . $session->getIdsession() . "'>{$session->getName()}</a></td>\n
                                           <td>" . $session->getStartdate() . "</td>\n
                                           <td>" . $session->getEnddate() . "</td>\n
                                           <td>{$session->getRegisterCount()} / {$session->getNumberallowed()}</td>\n                                        
                                        </tr>\n";
                }

                $sessionTable .= "</tbody>\n</table>\n";

                parent::setVariable("sessionList", $sessionTable);
            }

            // Display event managers
            parent::setVariable("managerList", parent::templateFileContents("EventManagerList"));

            $managerList = "";

            foreach(AttendeeFactory::getAllManagingEvent($event->getIdevent()) as $manager)
            {
                $managerList .= "<li>{$manager->getName()}</li>";
            }

            parent::setVariable("managerList", $managerList);
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Event Not Found");
            parent::setVariable("content", "<p>The event requested was not found!</p>");
        }
    }
}