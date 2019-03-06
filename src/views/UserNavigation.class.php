<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/27/2019
 * Time: 2:56 PM
 */


namespace views;


use controllers\FrontController;
use factories\RoleFactory;

class UserNavigation extends View
{
    /**
     * UserNavigation constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        parent::setTemplateFromTemplate("UserNavigation");

        // Build navigation based on user role
        // This is very sloppy and should be done w/ pages stored in a database

        $navigationLinks = "";
        $user = FrontController::getCurrentUser();
        $role = RoleFactory::getById($user->getRole());

        $navigationLinks .= "<li><a href='{{@baseURI}}myaccount'>My Account</a></li>\n";
        $navigationLinks .= "<li><a href='{{@baseURI}}events'>Events</a></li>\n";
        if($role->getName() == "admin" OR $role->getName() == "event manager") // Yuck, but that's what the database requires
        {
            $navigationLinks .= "<li><a href='{{@baseURI}}admin'>Admin</a></li>\n";
        }

        parent::setVariable("navigationLinks", $navigationLinks);
    }
}