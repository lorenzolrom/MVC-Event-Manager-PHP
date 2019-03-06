<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/27/2019
 * Time: 2:53 PM
 */


namespace views;


use controllers\FrontController;

class UserHeader extends View
{
    /**
     * UserHeader constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $navigation = new UserNavigation();
        parent::setTemplateFromTemplate("UserHeader");
        parent::setVariable("currentUserDisplayName", FrontController::getCurrentUser()->getName());
        parent::setVariable("navigation", $navigation->getHTML());
    }
}