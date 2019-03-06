<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/05/2019
 * Time: 8:29 AM
 */


namespace views;


use factories\VenueFactory;
use models\Event;
use models\Session;

class SessionForm extends View
{
    /**
     * SessionForm constructor.
     * @param Event $event
     * @param Session|null $session
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(Event $event, ?Session &$session = NULL)
    {
        parent::setTemplate(parent::templateFileContents("SessionForm"));

        // Add session information
        $venue = VenueFactory::getById($event->getVenue());
        parent::setVariable("eventname", $event->getName());
        parent::setVariable("venueName", $venue->getName());

        if($session !== NULL)
        {
            parent::setVariable("name", $session->getName());
            parent::setVariable("numberallowed", $session->getNumberallowed());

            // Convert datetime to two separate fields
            $startdatetime = $session->getStartdate();
            $enddatetime = $session->getEnddate();

            $startdate = substr($startdatetime, 0, 10);
            $starttime = substr($startdatetime, 11);
            $enddate = substr($enddatetime, 0, 10);
            $endtime = substr($enddatetime, 11);

            parent::setVariable("startdate", $startdate);
            parent::setVariable("starttime", $starttime);
            parent::setVariable("enddate", $enddate);
            parent::setVariable("endtime", $endtime);
            parent::setVariable("attendeecount", $session->getRegisterCount());
        }

        if(!empty($_POST))
        {
            if(isset($_POST['name']))
                parent::setVariable("name", $_POST['name']);
            if(isset($_POST['startdate']))
                parent::setVariable("startdate", $_POST['startdate']);
            if(isset($_POST['enddate']))
                parent::setVariable("enddate", $_POST['enddate']);
            if(isset($_POST['starttime']))
                parent::setVariable("starttime", $_POST['starttime']);
            if(isset($_POST['endtime']))
                parent::setVariable("endtime", $_POST['endtime']);
            if(isset($_POST['numberallowed']))
                parent::setVariable("numberallowed", $_POST['numberallowed']);
        }
    }
}