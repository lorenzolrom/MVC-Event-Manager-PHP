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
use exceptions\EntryNotFoundException;
use factories\EventFactory;
use factories\SessionFactory;
use factories\VenueFactory;

class EventPage extends View
{
    /**
     * EventPage constructor.
     * @param int $idevent
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
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

            $venue = VenueFactory::getById($event->getVenue());

            // Fill in event details
            parent::setVariable("idevent", $event->getIdevent());
            parent::setVariable("name", $event->getName());
            parent::setVariable("datestart", FrontController::convertToDisplayDate($event->getDatestart()));
            parent::setVariable("dateend", FrontController::convertToDisplayDate($event->getDateend()));
            parent::setVariable("venueName", $venue->getName());
            parent::setVariable("numberallowed", $event->getNumberallowed());

            // Get event sessions
            $sessions = SessionFactory::getAllEvent($event->getIdevent());
            if(empty($sessions))
                parent::setVariable("sessionList", "<span class='red-message'>NO DATA FOUND</span>");
            else
            {
                $sessionTable = "<table class='results'>\n<tbody>\n<tr><th>Name</th><th>Time</th><th>Attendance Status</th></tr>\n";

                foreach($sessions as $session)
                {
                    $sessionTable .= "<tr>\n
                                           <td><a href='{{@baseURI}}session/" . $session->getIdsession() . "'>{$session->getName()}</a></td>\n
                                           <td>" . FrontController::convertToDisplayDateTime($session->getStartdate()) . "</td>\n
                                           <td>{$session->getRegisterCount()} / {$session->getNumberallowed()}</td>\n                                        
                                        </tr>\n";
                }

                $sessionTable .= "</tbody>\n</table>\n";

                parent::setVariable("sessionList", $sessionTable);
            }
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Event Not Found");
            parent::setVariable("content", "<p>The event requested was not found!</p>");
        }
    }
}