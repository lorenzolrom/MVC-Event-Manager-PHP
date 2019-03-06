<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/27/2019
 * Time: 2:05 PM
 */


namespace views;


use controllers\FrontController;
use factories\EventFactory;
use factories\SessionFactory;
use factories\VenueFactory;

class EventListPage extends View
{

    /**
     * EventListPage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("content", self::templateFileContents("EventList"));

        // Generate a list of events from the database
        $eventList = "";

        foreach(EventFactory::getAll() as $event)
        {
            // Load venue
            $venue = VenueFactory::getById($event->getVenue());

            // Load sessions
            $sessions = "";

            $startDate = FrontController::convertToDisplayDate($event->getDatestart());
            $endDate = FrontController::convertToDisplayDate($event->getDateend());

            foreach(SessionFactory::getAllEvent($event->getIdevent()) as $session)
            {
                $sessionTime = FrontController::convertToDisplayDateTime($session->getStartdate());
                $sessions .= "<li><a href='{{@baseURI}}session/{$session->getIdsession()}'>$sessionTime</a> - '{$session->getName()}' ({$session->getRegisterCount()}/{$session->getNumberallowed()})</li>";
            }

            $sessions = (strlen($sessions) === 0) ? "None" : "<ul>$sessions</ul>";

            $eventList .= "<tr>
                                <td><a href='{{@baseURI}}events/" . $event->getIdevent() . "'>{$event->getName()}</a></td>
                                <td>{$venue->getName()}</td>
                                <td>$startDate through $endDate</td>
                                <td>{$sessions}</td>
                            </tr>";
        }

        parent::setVariable("eventList", $eventList);
        parent::setVariable("pageTitle", "Events");
    }
}