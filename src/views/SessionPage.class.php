<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/02/2019
 * Time: 2:28 PM
 */


namespace views;


use controllers\FrontController;
use exceptions\EntryNotFoundException;
use factories\EventFactory;
use factories\SessionFactory;
use factories\VenueFactory;

class SessionPage extends View
{
    /**
     * SessionPage constructor.
     * @param int $idsession
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(int $idsession)
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        try
        {
            $session = SessionFactory::getById($idsession);
            $event = EventFactory::getById($session->getEvent());
            $venue = VenueFactory::getById($event->getVenue());
            parent::setVariable("pageTitle", "Session: " . $session->getName());
            parent::setVariable("content", self::templateFileContents("Session"));

            // Fill in event details
            parent::setVariable("idevent", $event->getIdevent());
            parent::setVariable("eventname", "<a href='{{@baseURI}}events/{$event->getIdevent()}'>{$event->getName()}</a>");
            parent::setVariable("venueName", $venue->getName());

            // Fill in session details
            parent::setVariable("idsession", $session->getIdsession());
            parent::setVariable("sessionname", $session->getName());
            parent::setVariable("attendeecount", $session->getRegisterCount());
            parent::setVariable("numberallowed", $session->getNumberallowed());
            parent::setVariable("startdate", FrontController::convertToDisplayDateTime($session->getStartdate()));
            parent::setVariable("enddate", FrontController::convertToDisplayDateTime($session->getEnddate()));

            // Determine buttons to display
            if(FrontController::getCurrentUser()->isRegisteredForSession(intval($idsession)))
                parent::setVariable("buttons", "<a class='button' href='{{@baseURI}}session/{$session->getIdsession()}/unregister'>Un-Register</a>");
            else
                parent::setVariable("buttons", "<a class='button' href='{{@baseURI}}session/{$session->getIdsession()}/register'>Register</a>");
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Session Not Found");
            parent::setVariable("content", "<p>The session requested was not found</p>");
        }
    }
}