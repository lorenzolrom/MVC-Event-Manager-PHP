<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:59 PM
 */


namespace views;


use Exception;

class ErrorPage extends View
{

    /**
     * Error constructor.
     */
    public function __construct()
    {
        try
        {
            $page = new UserBasePage();
            parent::setTemplate($page->getTemplate());
            parent::setVariable("content", View::templateFileContents("Error"));
            parent::setVariable("pageTitle", "An Error Occurred");
        }
        catch(Exception $e)
        {
            // If error page cannot be displayed, there is no recovery path
            die($e->getMessage());
        }
    }

    /**
     * @param Exception $e
     */
    public function setException(Exception $e)
    {
        parent::setVariable("errorMessage", "{$e->getMessage()}");
    }
}