<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/02/2019
 * Time: 5:23 PM
 */


namespace views;


use controllers\FrontController;
use factories\EventFactory;
use factories\VenueFactory;

class MyAccountPage extends View
{
    /**
     * MyAccountPage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("pageTitle", "My Account");
        parent::setVariable("content", self::templateFileContents("MyRegistrations"));

        // Generate list of current registrations
        $sessions = "";

        foreach(FrontController::getCurrentUser()->getRegisteredSessions() as $session)
        {
            $event = EventFactory::getById($session->getEvent());
            $venue = VenueFactory::getById($event->getVenue());

            $sessions .= "<tr>
                                <td><a href='{{@baseURI}}session/" . $session->getIdsession() . "'>{$session->getName()}</a></td>                            
                                <td>{$event->getName()}</td>                            
                                <td>{$venue->getName()}</td>                            
                                <td>" . FrontController::convertToDisplayDateTime($session->getStartdate()) . "</td>                            
                            </tr>";
        }

        parent::setVariable("sessionList", $sessions);
    }
}