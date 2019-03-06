<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/04/2019
 * Time: 2:19 PM
 */


namespace views;


use exceptions\EntryNotFoundException;
use factories\EventFactory;

class EventEditAdminPage extends View
{
    /**
     * EventEditAdminPage constructor.
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
            // Load event
            $event = EventFactory::getById($idevent);
            $eventForm = new EventAdminForm($event);
            parent::setVariable("pageTitle", "Edit Event: {$event->getName()}");
            parent::setVariable("content", $eventForm->getTemplate());
            parent::setVariable("parentPage", "admin/events/view/{{@idevent}}");
            parent::setVariable("idevent", $event->getIdevent());

        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Event Not Found");
            parent::setVariable("content", "<p>Event requested was not found!</p>");
        }
    }
}