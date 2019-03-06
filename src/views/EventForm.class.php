<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/04/2019
 * Time: 2:43 PM
 */


namespace views;


use controllers\FrontController;
use factories\VenueFactory;
use models\Event;

class EventForm extends View
{
    /**
     * EventForm constructor.
     * @param Event|null $event
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(?Event &$event = NULL)
    {
        parent::setTemplate(parent::templateFileContents("EventForm"));

        // Generate Venue Options
        $venueOptions = "";

        foreach(VenueFactory::getAll() as $venue)
        {
            if(isset($_POST['venue']) AND $_POST['venue'] == $venue->getIdvenue())
                $selected = "selected";
            else if($event !== NULL AND $event->getVenue() == $venue->getIdvenue())
                $selected = "selected";
            else
                $selected = "";

            $venueOptions .= "<option value='{$venue->getIdvenue()}' $selected>{$venue->getName()}</option>";
        }

        parent::setVariable("venueList", $venueOptions);

        // Fill in information from Event, if present
        if($event !== NULL)
        {
            parent::setVariable("idevent", $event->getIdevent());
            parent::setVariable("name", $event->getName());
            parent::setVariable("datestart", FrontController::convertToDisplayDate($event->getDatestart(), 'Y-m-d'));
            parent::setVariable("dateend", FrontController::convertToDisplayDate($event->getDateend(), 'Y-m-d'));
            parent::setVariable("numberallowed", $event->getNumberallowed());
        }

        // Fill in existing POST data
        if(!empty($_POST))
        {
            if(isset($_POST['name']))
                parent::setVariable("", $_POST['name']);
            if(isset($_POST['datestart']))
                parent::setVariable("", $_POST['datestart']);
            if(isset($_POST['dateend']))
                parent::setVariable("", $_POST['dateend']);
            if(isset($_POST['numberallowed']))
                parent::setVariable("", $_POST['numberallowed']);
        }
    }
}