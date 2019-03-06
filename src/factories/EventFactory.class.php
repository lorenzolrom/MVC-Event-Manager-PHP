<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:02 PM
 */


namespace factories;


use database\EventDatabaseHandler;
use models\Event;

class EventFactory
{
    /**
     * @param int $idevent
     * @return Event
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getById(int $idevent): Event
    {
        return EventDatabaseHandler::selectById($idevent);
    }

    /**
     * @param string $name
     * @return Event
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getByName(string $name): Event
    {
        return EventDatabaseHandler::selectByName($name);
    }

    /**
     * @return Event[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAll(): array
    {
        return EventDatabaseHandler::selectAll();
    }

    /**
     * @param int $idattendee
     * @return Event[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllByManager(int $idattendee): array
    {
        return EventDatabaseHandler::selectAllFromManager($idattendee);
    }

    /**
     * @param int $idvenue
     * @return Event[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllByVenue(int $idvenue): array
    {
        return EventDatabaseHandler::selectAllFromVenue($idvenue);
    }

    /**
     * @param string $name
     * @param string $datestart
     * @param string $dateend
     * @param int $numberallowed
     * @param int $venue
     * @return Event
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getNew(string $name, string $datestart, string $dateend, int $numberallowed, int $venue): Event
    {
        return EventDatabaseHandler::selectById(EventDatabaseHandler::insert($name, $datestart, $dateend, $numberallowed, $venue));
    }
}