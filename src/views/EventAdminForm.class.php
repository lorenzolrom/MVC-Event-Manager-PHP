<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/04/2019
 * Time: 8:49 PM
 */


namespace views;


use factories\AttendeeFactory;
use models\Event;

class EventAdminForm extends EventForm
{
    public function __construct(?Event $event = NULL)
    {
        parent::__construct($event);

        // Add manager select
        parent::setVariable("managerSelect", parent::templateFileContents("EventManagerSelect"));

        $managerIds = array();

        if($event !==  NULL)
        {
            foreach (AttendeeFactory::getAllManagingEvent($event->getIdevent()) as $manager)
            {
                $managerIds[] = $manager->getIdattendee();
            }
        }

        if(isset($_POST['managers']) AND is_array($_POST['managers']))
        {
            foreach($_POST['managers'] as $idattendee)
            {
                $managerIds[] = $idattendee;
            }
        }

        $managerOptions = "";

        foreach(AttendeeFactory::getAll() as $attendee)
        {
            $selected = in_array($attendee->getIdattendee(), $managerIds) ? "selected" : "";

            $managerOptions .= "<option value='{$attendee->getIdattendee()}' $selected>{$attendee->getName()}</option>";
        }

        parent::setVariable("managerSelect", $managerOptions);
    }
}