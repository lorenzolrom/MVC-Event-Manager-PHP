<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/06/2019
 * Time: 7:12 AM
 */


namespace views;


use exceptions\EntryNotFoundException;
use factories\SessionFactory;

class SessionAddAttendeePage extends View
{
    /**
     * SessionAddAttendeePage constructor.
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

            parent::setVariable("pageTitle", "Register Attendee For Session: {$session->getName()}");
            parent::setVariable("content", self::templateFileContents("RegisterForm"));
            parent::setVariable("idsession", $session->getIdsession());
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Session Not Found");
            parent::setVariable("content", "<p>Requested Session Was Not Found!</p>");
        }
    }
}