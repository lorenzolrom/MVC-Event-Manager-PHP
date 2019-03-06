<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 2:00 PM
 */


namespace views;


use factories\RoleFactory;
use models\Attendee;

class AttendeeForm extends View
{
    /**
     * AttendeeForm constructor.
     * @param null|Attendee $attendee
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(?Attendee &$attendee = NULL)
    {
        // Load form
        parent::setTemplate(parent::templateFileContents("AttendeeForm"));

        // Generate role select
        $roleOptions = "";

        foreach(RoleFactory::getAll() as $role)
        {
            // Determine default role to be selected
            if(isset($_POST['roles']) AND $_POST['roles'] == $role->getIdroles())
                $selected = " selected";
            else if($attendee !== NULL AND $attendee->getRole() == $role->getIdroles())
                $selected = " selected";
            else
                $selected = "";

            $roleOptions .= "<option value='{$role->getIdroles()}' $selected>{$role->getName()}</option>\n";
        }

        parent::setVariable("roleOptions", $roleOptions);

        // Fill in information from Attendee, if present
        if($attendee !== NULL)
        {
            parent::setVariable("name", $attendee->getName());
        }

        // Fill in existing POST data
        if(!empty($_POST))
        {
            if(isset($_POST['name']))
                parent::setVariable("name", $_POST['name']);
        }
    }
}