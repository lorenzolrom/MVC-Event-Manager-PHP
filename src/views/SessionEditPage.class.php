<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/05/2019
 * Time: 4:34 PM
 */


namespace views;


use exceptions\EntryNotFoundException;
use factories\EventFactory;
use factories\SessionFactory;

class SessionEditPage extends View
{
    /**
     * SessionEditPage constructor.
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
            // Load session
            $session = SessionFactory::getById($idsession);
            $event = EventFactory::getById($session->getEvent());
            $sessionForm = new SessionForm($event, $session);
            parent::setVariable("pageTitle", "Edit Session: {$session->getName()}");
            parent::setVariable("content", $sessionForm->getTemplate());
            parent::setVariable("idsession", $session->getIdsession());
            parent::setVariable("parentPage", "admin/sessions/view/{$session->getIdsession()}");
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Session Not Found");
            parent::setVariable("content", "<p>Session requested was not found!</p>");
        }
    }
}