<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 6:32 PM
 */


namespace views;


use exceptions\EntryNotFoundException;
use factories\VenueFactory;

class EditVenuePage extends View
{
    /**
     * EditVenuePage constructor.
     * @param int $idvenue
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct(int $idvenue)
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());

        try
        {
            $venue = VenueFactory::getById($idvenue);

            $venueForm = new VenueForm($venue);
            parent::setVariable("pageTitle", "Edit Venue: " . $venue->getName());
            parent::setVariable("content", $venueForm->getTemplate());
        }
        catch(EntryNotFoundException $e)
        {
            parent::setVariable("pageTitle", "Venue Not Found");
            parent::setVariable("content", "<p>The venue requested was not found!</p>");
        }
    }
}