<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/05/2019
 * Time: 11:49 AM
 */


namespace views;


use controllers\FrontController;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\SecurityException;
use factories\EventFactory;

class SessionCreatePage extends View
{
    /**
     * SessionCreatePage constructor.
     * @param int $idevent
     * @throws EntryNotFoundException
     * @throws SecurityException
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

            try
            {
                FrontController::validateRole(['admin']);
            }
            catch(SecurityException $e)
            {
                // Check if user has rights to this event
                if(!FrontController::getCurrentUser()->isAManager($event->getIdevent()))
                {
                    throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);
                }
            }

            $sessionForm = new SessionForm($event);

            parent::setVariable("pageTitle", "Create Session for Event: {$event->getName()}");
            parent::setVariable("content", $sessionForm->getTemplate());
            parent::setVariable("parentPage", "admin/events/view/{$event->getIdevent()}");

        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Event Is Invalid");
            parent::setVariable("content", "<p>Event requested was not found</p>");
        }
    }
}