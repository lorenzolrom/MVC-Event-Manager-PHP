<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/02/2019
 * Time: 2:33 PM
 */


namespace controllers;


use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\RouteException;
use factories\SessionFactory;
use views\SessionPage;

class SessionController extends Controller
{

    /**
     * @param string $uri
     * @return string
     * @throws \exceptions\DatabaseException
     * @throws RouteException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function getPage(string $uri): string
    {
        FrontController::validateToken();
        $uriParts = explode("/", $uri);

        if(sizeof($uriParts) == 2) // Load specific session
        {
            $sessionPage = new SessionPage(intval($uriParts[1]));
            return $sessionPage->getHTML();
        }
        else if(sizeof($uriParts) == 3 AND $uriParts[2] == "register")
        {
            // Check for session
            try
            {
                $session = SessionFactory::getById(intval($uriParts[1]));
                if($session->getRegisterCount() >= $session->getNumberallowed())
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "session/" . $session->getIdsession() . "?ERROR=Session Is Full");
                    exit;
                }
                else if(FrontController::getCurrentUser()->registerForSession($session->getIdsession()))
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "session/" . $session->getIdsession() . "?NOTICE=Successfully Registered For Session");
                    exit;
                }
                else
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "session/" . $session->getIdsession() . "?ERROR=You Have Already Registered For This Session");
                    exit;
                }
            }
            catch(EntryNotFoundException $e)
            {
                $sessionPage = new SessionPage(intval($uriParts[1]));
                return $sessionPage->getHTML();
            }
        }
        else if(sizeof($uriParts) == 3 AND $uriParts[2] == "unregister")
        {
            // Check for session
            try
            {
                $session = SessionFactory::getById(intval($uriParts[1]));
                if(FrontController::getCurrentUser()->unregisterFromSession($session->getIdsession()))
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "session/" . $session->getIdsession() . "?NOTICE=Successfully Un-Registered From Session");
                    exit;
                }
                else
                {
                    header("Location: " . FB_CONFIG['baseURI'] . "session/" . $session->getIdsession() . "?ERROR=You Are Not Registered For This Session");
                    exit;
                }
            }
            catch(EntryNotFoundException $e)
            {
                $sessionPage = new SessionPage(intval($uriParts[1]));
                return $sessionPage->getHTML();
            }
        }

        throw new RouteException(ErrorMessages::PAGE_NOT_FOUND . ": $uri", RouteException::ROUTE_URI_NOT_FOUND);
    }
}