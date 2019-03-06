<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 2:07 PM
 */


namespace views;


class CreateAttendeePage extends View
{
    /**
     * CreateAttendeePage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("pageTitle", "Create Attendee");

        $attendeeForm = new AttendeeForm();
        parent::setVariable("content", $attendeeForm->getTemplate());
        parent::setVariable("passwordRequired", "class=\"required\"");
    }
}