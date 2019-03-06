<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:53 PM
 */


namespace controllers;


use controllers\factories\ControllerFactory;
use Exception;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\SecurityException;
use exceptions\TokenException;
use exceptions\ViewException;
use factories\AttendeeFactory;
use factories\RoleFactory;
use factories\TokenFactory;
use models\Attendee;
use views\ErrorPage;

class FrontController
{
    /**
     * @return string
     */
    public static function getPage(): string
    {

        // Retrieve requested URL
        if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
            $fb_requestedURL = "https";
        else
            $fb_requestedURL = "http";

        $fb_requestedURL .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $fb_requestedURI = "/" . explode(FB_CONFIG['baseURL'] . FB_CONFIG['baseURI'], $fb_requestedURL)[1]; // Get portion of URL after application
        $fb_requestedURIParts = explode('/', explode('?', $fb_requestedURI)[0]); // Break URI into pieces (ignore GET variables)

        // If no URI is supplied, direct user to the defined default page
        if(empty($fb_requestedURIParts[1]))
        {
            header("Location: " . FB_CONFIG['baseURL'] . FB_CONFIG['baseURI'] . FB_CONFIG['defaultPage']);
            exit;
        }

        /*
         * Locate and load controller for route
         */

        try
        {
            $controller = ControllerFactory::getController($fb_requestedURIParts[1]);

            // Build requested page URI
            $fb_completeRequestURI = "";

            for($i = 1; $i < sizeof($fb_requestedURIParts); $i++)
            {
                $fb_completeRequestURI .= $fb_requestedURIParts[$i] . "/";
            }

            $fb_completeRequestURI = rtrim($fb_completeRequestURI, "/"); // Remove trailing slash

            $output = $controller->getPage($fb_completeRequestURI);
        }
        catch(Exception $e)
        {
            try
            {
                $errorPage = new ErrorPage();
                $errorPage->setException($e);
                $output = $errorPage->getHTML();
            }
            catch (ViewException $e)
            {
                // Error page view not found, this is un-recoverable
                die($e->getMessage());
            }
        }

        return $output;
    }

    /**
     * @return int ID of user the token is for
     * @throws \exceptions\DatabaseException
     */
    public static function validateToken(): int
    {
        try
        {
            if(!isset($_COOKIE[FB_CONFIG['cookieName']]))
                throw new SecurityException(ErrorMessages::MUST_LOGIN, SecurityException::AUTHENTICATION_REQUIRED);

            try
            {
                $token = TokenFactory::getByToken($_COOKIE[FB_CONFIG['cookieName']]);
                $token->validate();
                return $token->getIdattendee();
            }
            catch (EntryNotFoundException $e)
            {
                throw new SecurityException(ErrorMessages::MUST_LOGIN, SecurityException::AUTHENTICATION_REQUIRED);
            }
            catch (TokenException $e)
            {
                throw new SecurityException(ErrorMessages::TOKEN_IS_EXPIRED, SecurityException::AUTHENTICATION_REQUIRED);
            }
        }
        catch(SecurityException $e)
        {
            header("Location: " . FB_CONFIG['baseURI'] . "login?NOTICE=" . $e->getMessage());
            exit();
        }
    }

    /**
     * @return Attendee
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function getCurrentUser(): Attendee
    {
        return AttendeeFactory::getById(self::validateToken());
    }

    /**
     * Convert SQL datetime to m/d/Y format (hey, maybe this should be CONFIGURABLE
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public static function convertToDisplayDate(string $datetime, string $format = 'm/d/Y'): string
    {
        return date($format, strtotime($datetime));
    }

    /**
     * Converts SQL datetime to m/d/Y H:i format
     * @param string $datetime
     * @return string
     */
    public static function convertToDisplayDateTime(string $datetime): string
    {
        return date('m/d/Y H:i', strtotime($datetime));
    }

    /**
     * @param array $roles
     * @return bool
     * @throws EntryNotFoundException
     * @throws SecurityException
     * @throws \exceptions\DatabaseException
     */
    public static function validateRole(array $roles): bool
    {
        // Validate permissions
        $role = RoleFactory::getById(FrontController::getCurrentUser()->getRole());

        if(!in_array($role->getName(), $roles))
            throw new SecurityException(ErrorMessages::PAGE_NO_PERMISSION, SecurityException::USER_DOES_NOT_HAVE_PERMISSION);

        return TRUE;
    }
}