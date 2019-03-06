<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/02/2019
 * Time: 5:11 PM
 */


namespace controllers;


use exceptions\ErrorMessages;
use exceptions\RouteException;
use exceptions\SecurityException;
use views\ChangePasswordPage;
use views\MyAccountPage;
use views\View;

class MyAccountController extends Controller
{

    /**
     * @param string $uri
     * @return string
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     * @throws RouteException
     */
    public function getPage(string $uri): string
    {
        FrontController::validateToken();
        $uriParts = explode("/", $uri);

        if($uri == "myaccount")
        {
            $myAccountPage = new MyAccountPage();
            return $myAccountPage->getHTML();
        }
        else if(sizeof($uriParts) == 2 AND $uriParts[1] == "changepassword")
        {
            $changePasswordPage = new ChangePasswordPage();

            // Process change password request
            if(!empty($_POST))
            {
                $errors = $this->processChangePassword($_POST);

                if(!empty($errors))
                {
                    $changePasswordPage->setVariable("notifications", View::templateFileContents("Notifications"));
                    $changePasswordPage->setVariable("notificationTitle", "Error");
                    $changePasswordPage->setVariable("notificationClass", "notifications-error");

                    $errorString = "<ul>";

                    foreach($errors as $error)
                    {
                        $errorString .= "<li>$error</li>";
                    }

                    $errorString .= "</ul>";

                    $changePasswordPage->setVariable("notifications", $errorString);
                }
            }

            return $changePasswordPage->getHTML();
        }

        throw new RouteException(ErrorMessages::PAGE_NOT_FOUND . ": $uri", RouteException::ROUTE_URI_NOT_FOUND);
    }

    /**
     * @param array $formData
     * @return array
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    private function processChangePassword(array $formData): array
    {
        $errors = array();

        if(!isset($formData['password']) OR strlen($formData['password']) == 0)
            $errors[] = "Current Password Required";
        if(!isset($formData['new']) OR strlen($formData['new']) == 0)
            $errors[] = "New Password Required";

        if(!empty($errors))
            return $errors;

        // Validate current password
        try
        {
            FrontController::getCurrentUser()->passwordIsCorrect($formData['password']);

            // Verify new and confirm passwords are the same
            if($formData['new'] != $formData['confirm'])
                $errors[] = "New Passwords Do Not Match";

            // Update user's password
            FrontController::getCurrentUser()->setPassword($formData['new']);

            header("Location: " . FB_CONFIG['baseURI'] . "myaccount?NOTICE=Password Updated Successfully");
            exit;
        }
        catch(SecurityException $e)
        {
            $errors[] = "Current Password Is Incorrect";
        }

        return $errors;
    }
}