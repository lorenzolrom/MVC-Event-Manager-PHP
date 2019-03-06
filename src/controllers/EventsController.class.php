<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:44 PM
 */


namespace controllers;


use exceptions\ErrorMessages;
use exceptions\RouteException;
use views\EventListPage;
use views\EventPage;

class EventsController extends Controller
{

    /**
     * @param string $uri
     * @return string
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\ViewException
     * @throws \exceptions\EntryNotFoundException
     * @throws RouteException
     */
    public function getPage(string $uri): string
    {
        FrontController::validateToken();
        $uriParts = explode("/", $uri);

        if($uri == "events") // Display all events
        {
            $eventListPage = new EventListPage();
            return $eventListPage->getHTML();
        }
        else if(sizeof($uriParts) == 2) // Load specific event
        {
            $eventPage = new EventPage(intval($uriParts[1]));
            return $eventPage->getHTML();
        }

        throw new RouteException(ErrorMessages::PAGE_NOT_FOUND . ": $uri", RouteException::ROUTE_URI_NOT_FOUND);
    }
}