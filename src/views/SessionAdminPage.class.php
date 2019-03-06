<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/05/2019
 * Time: 3:34 PM
 */


namespace views;


use controllers\FrontController;
use exceptions\EntryNotFoundException;
use factories\EventFactory;
use factories\SessionFactory;
use factories\VenueFactory;

class SessionAdminPage extends View
{
    /**
     * SessionAdminPage constructor.
     * @param int $idsession
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\ViewException
     */
    public function __construct(int $idsession)
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        try
        {
            $session = SessionFactory::getById($idsession);
            parent::setVariable("pageTitle", "Session: " . $session->getName());
            parent::setVariable("content", self::templateFileContents("Session"));

            $event = EventFactory::getById($session->getEvent());
            $venue = VenueFactory::getById($event->getVenue());

            // Set @buttons
            parent::setVariable("buttons", "
                <a class='button' href='{{@baseURI}}admin/sessions/edit/" . $session->getIdsession() . "'>Edit</a>            
                <a class='button' href='{{@baseURI}}admin/sessions/edit/" . $session->getIdsession() . "/register'>Add Attendee</a>            
                <a class='button' href='{{@baseURI}}admin/sessions/delete/" . $session->getIdsession() . "'>Delete</a>            
            ");

            // Fill in session details
            parent::setVariable("eventname", "<a href='{{@baseURI}}admin/events/view/{$event->getIdevent()}'>{$event->getName()}</a>");
            parent::setVariable("venueName", $venue->getName());
            parent::setVariable("sessionname", $session->getName());
            parent::setVariable("numberallowed", $session->getNumberallowed());
            parent::setVariable("attendeecount", $session->getRegisterCount());
            parent::setVariable("startdate", FrontController::convertToDisplayDateTime($session->getStartdate()));
            parent::setVariable("enddate", FrontController::convertToDisplayDateTime($session->getEnddate()));

            parent::setVariable("attendeeList", self::templateFileContents("AttendeeList"));

            $attendeeList = "";

            // Add attendees
            foreach($session->getAttendees() as $attendee)
            {
                $attendeeList .= "<tr><td>{$attendee->getName()}</td><td><a class='button confirm-button delete-button' href='{{@baseURI}}admin/sessions/{$session->getIdsession()}/unregister/{$attendee->getIdattendee()}'>Remove</a></td></tr>";
            }

            parent::setVariable("attendeeList", $attendeeList);
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Session Not Found");
            parent::setVariable("content", "Requested Session Was Not Found");
        }
    }
}