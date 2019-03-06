<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:46 PM
 */


namespace views;


use controllers\FrontController;
use factories\AttendeeFactory;
use factories\EventFactory;
use factories\RoleFactory;
use factories\VenueFactory;

class AdminPage extends View
{
    /**
     * AdminPage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $role = RoleFactory::getById(FrontController::getCurrentUser()->getRole());
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("pageTitle", "Admin");
        parent::setVariable("content", View::templateFileContents("AdminPage"));

        // Display areas user has permission to use
        parent::setVariable("managerDashboard", View::templateFileContents("EventManagerDashboard"));

        if($role->getName() == "admin")
        {
            parent::setVariable("adminDashboard", View::templateFileContents("AdminDashboard"));

            // Populate attendee table
            $attendees = "";
            foreach(AttendeeFactory::getAll() as $attendee)
            {
                $role = RoleFactory::getById($attendee->getRole());
                $attendees .= "<tr>
                                    <td><a href='{{@baseURI}}admin/attendees/edit/" . $attendee->getIdattendee() . "'>{$attendee->getName()}</a></td>
                                    <td>{$role->getName()}</td>
                                    <td><a class='button confirm-button delete-button' href='{{@baseURI}}admin/attendees/delete/" . $attendee->getIdattendee() . "'>Delete</a></td>
                                </tr>";
            }

            parent::setVariable("attendeeList", $attendees);

            // Populate venue table
            $venues = "";

            foreach(VenueFactory::getAll() as $venue)
            {
                $venues .= "<tr>
                                <td><a href='{{@baseURI}}admin/venues/edit/" . $venue->getIdvenue() . "'>{$venue->getName()}</a></td>
                                <td>{$venue->getCapacity()}</td>
                                <td><a class='button confirm-button delete-button' href='{{@baseURI}}admin/venues/delete/" . $venue->getIdvenue() . "'>Delete</a></td>
                            </tr>";
            }

            parent::setVariable("venueList", $venues);

            // Populate all events
            $events = "";

            foreach(EventFactory::getAll() as $event)
            {
                $venue = VenueFactory::getById($event->getVenue());

                $events .= "<tr>
                                <td><a href='{{@baseURI}}admin/events/view/" . $event->getIdevent() . "'>{$event->getName()}</a></td>
                                <td>{$venue->getName()}</td>
                                <td>" . FrontController::convertToDisplayDate($event->getDatestart()) . "</td>
                                <td>" . FrontController::convertToDisplayDate($event->getDateend()) . "</td>
                                <td>{$event->getNumberallowed()}</td>
                            </tr>";
            }

            parent::setVariable("eventList", $events);
        }

        // Populate my events table
        $myevents = "";

        foreach(EventFactory::getAllByManager(FrontController::getCurrentUser()->getIdattendee()) as $event)
        {
            $venue = VenueFactory::getById($event->getVenue());
            $myevents .= "<tr>
                    <td><a href='{{@baseURI}}admin/events/view/" . $event->getIdevent() . "'>{$event->getName()}</a></td>
                    <td>{$venue->getName()}</td>
                    <td>" . FrontController::convertToDisplayDate($event->getDatestart()) . "</td>
                    <td>" . FrontController::convertToDisplayDate($event->getDateend()) . "</td>
                    <td>{$event->getNumberallowed()}</td>
                </tr>";
        }

        parent::setVariable("myEventList", $myevents);
    }
}