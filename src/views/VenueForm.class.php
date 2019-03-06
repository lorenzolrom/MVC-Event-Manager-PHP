<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 6:34 PM
 */


namespace views;


use models\Venue;

class VenueForm extends View
{
    /**
     * VenueForm constructor.
     * @param Venue|null $venue
     * @throws \exceptions\ViewException
     */
    public function __construct(?Venue &$venue = NULL)
    {
        // Load form
        parent::setTemplate(parent::templateFileContents("VenueForm"));

        // Fill in data from Venue, if present
        if($venue !== NULL)
        {
            parent::setVariable("name", $venue->getName());
            parent::setVariable("capacity", $venue->getCapacity());
        }
        else if(!empty($_POST)) // Fill in data from form submission
        {
            if(isset($_POST['name']))
                parent::setVariable("name", $_POST['name']);
            if(isset($_POST['capacity']))
                parent::setVariable("capacity", $_POST['capacity']);
        }
    }
}