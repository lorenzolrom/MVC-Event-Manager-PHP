<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 8:28 PM
 */


namespace views;


class CreateVenuePage extends View
{
    /**
     * CreateVenuePage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("pageTitle", "Create Venue");

        $venueForm = new VenueForm();
        parent::setVariable("content", $venueForm->getTemplate());
    }
}