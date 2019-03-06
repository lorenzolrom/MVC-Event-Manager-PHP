<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:14 PM
 */


namespace controllers\factories;


use controllers\AdminController;
use controllers\Controller;
use controllers\EventsController;
use controllers\LoginController;
use controllers\MyAccountController;
use controllers\SessionController;
use exceptions\ControllerException;
use exceptions\ErrorMessages;

class ControllerFactory
{
    /**
     * @param string $route
     * @return Controller
     * @throws ControllerException
     */
    public static function getController(string $route): Controller
    {
        switch($route)
        {
            case "login":
                return new LoginController();
            case "logout":
                return new LoginController();
            case "events":
                return new EventsController();
            case "session":
                return new SessionController();
            case "admin":
                return new AdminController();
            case "myaccount":
                return new MyAccountController();
            default:
                throw new ControllerException(ErrorMessages::CONTROLLER_NOT_FOUND . ": $route", ControllerException::CONTROLLER_NOT_FOUND);
        }
    }
}