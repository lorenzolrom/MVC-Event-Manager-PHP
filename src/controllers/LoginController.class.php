<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:13 PM
 */


namespace controllers;


use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\SecurityException;
use exceptions\URLException;
use factories\AttendeeFactory;
use factories\TokenFactory;
use views\LoginPage;
use views\View;

class LoginController extends Controller
{

    /**
     * @param string $uri
     * @return string
     * @throws URLException
     * @throws \exceptions\ViewException
     */
    public function getPage(string $uri): string
    {
        switch($uri)
        {
            case "login":
                return $this->getLoginForm();
            case "logout":
                return $this->processLogout();
            default:
                throw new URLException(ErrorMessages::PAGE_NOT_FOUND . ": $uri", URLException::PAGE_NOT_FOUND);
        }
    }

    /**
     * @return string
     * @throws \exceptions\ViewException
     */
    private function getLoginForm(): string
    {
        $loginPage = new LoginPage();

        try
        {
            // Process login
            if(!empty($_POST))
            {
                $this->processLogin($_POST);
                header("Location: " . FB_CONFIG['baseURI']);
                exit;
            }

            // Check for existing session
            if(isset($_COOKIE[FB_CONFIG['cookieName']]))
            {
                TokenFactory::getByToken($_COOKIE[FB_CONFIG['cookieName']])->validate();
                header("Location: " . FB_CONFIG['baseURI']);
                exit();
            }
        }
        catch(EntryNotFoundException $e)
        {
            // Do nothing, display login form
        }
        catch(\Exception $e)
        {
            $loginPage->setVariable("notifications", View::templateFileContents("Notifications"));
            $loginPage->setVariable("notificationTitle", "Error");
            $loginPage->setVariable("notificationClass", "notifications-error");
            $loginPage->setVariable("notifications", "{$e->getMessage()}");

            // Default username, if available
            if(isset($_POST['username']))
                $loginPage->setVariable("username", $_POST['username']);
        }

        if(isset($_GET['NOTICE']))
        {
            $loginPage->setVariable("notifications", View::templateFileContents("Notifications"));
            $loginPage->setVariable("notificationTitle", "Notice");
            $loginPage->setVariable("notificationClass", "notifications-notice");
            $loginPage->setVariable("notifications", $_GET['NOTICE']);
        }

        return $loginPage->getHTML();
    }

    /**
     * @param array $formData
     * @return bool
     * @throws SecurityException
     * @throws \exceptions\DatabaseException
     */
    private function processLogin(array $formData): bool
    {
        // Must submit username and password
        if(!isset($formData['username']) OR !isset($formData['password']))
            throw new SecurityException(ErrorMessages::PASSWORD_IS_INCORRECT, SecurityException::USER_NOT_FOUND);

        $formData['username'] = strtolower($formData['username']);

        try
        {
            // Get and authenticate user
            $loginUser = AttendeeFactory::getByName($formData['username']);
            $loginUser->authenticate($formData['password']);
            return TRUE;
        }
        catch(EntryNotFoundException $e)
        {
            throw new SecurityException(ErrorMessages::USER_NOT_FOUND, SecurityException::USER_NOT_FOUND);
        }
    }

    /**
     * Logs out a user
     */
    private function processLogout()
    {
        try
        {
            // If no cookie, redirect to login page
            if(isset($_COOKIE[FB_CONFIG['cookieName']]))
            {
                // Check token
                $token = TokenFactory::getByToken($_COOKIE[FB_CONFIG['cookieName']]);
                $user = AttendeeFactory::getById($token->getIdattendee());
                $user->logout();

                header("Location: " . FB_CONFIG['baseURI'] . "login?NOTICE=Successfully Logged Out");
                exit();
            }
        }
        catch(\Exception $e)
        {
            // Redirect to login
        }

        header("Location: " . FB_CONFIG['baseURI'] . "login");
        exit();
    }
}