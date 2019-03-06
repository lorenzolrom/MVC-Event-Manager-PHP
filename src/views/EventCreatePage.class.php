<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/05/2019
 * Time: 6:54 AM
 */


namespace views;


class EventCreatePage extends View
{
    /**
     * EventCreateAdminPage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        $eventForm = new EventAdminForm();
        parent::setVariable("pageTitle", "Create Event");
        parent::setVariable("content", $eventForm->getTemplate());
        parent::setVariable("parentPage", "admin/");
    }
}