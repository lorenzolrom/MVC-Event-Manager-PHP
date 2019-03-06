<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 1:57 PM
 */


namespace views;


use exceptions\EntryNotFoundException;
use factories\AttendeeFactory;

class EditAttendeePage extends View
{
    /**
     * EditAttendeePage constructor.
     * @param int $idAttendee
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(int $idAttendee)
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        try
        {
            $attendee = AttendeeFactory::getById($idAttendee);

            $attendeeForm = new AttendeeForm($attendee);
            parent::setVariable("pageTitle", "Edit Attendee: " . $attendee->getName());
            parent::setVariable("content", $attendeeForm->getTemplate());
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Attendee Not Found");
            parent::setVariable("content", "<p>The attendee requested was not found!</p>");
        }
    }
}