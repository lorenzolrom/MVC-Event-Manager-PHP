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


use database\SessionDatabaseHandler;
use models\Session;

class SessionFactory
{
    /**
     * @param int $idsession
     * @return Session
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getById(int $idsession): Session
    {
        return SessionDatabaseHandler::selectById($idsession);
    }

    /**
     * @param int $idevent
     * @return Session[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllEvent(int $idevent): array
    {
        return SessionDatabaseHandler::selectByEvent($idevent);
    }

    /**
     * @param int $idattendee
     * @return Session[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllByAttendee(int $idattendee): array
    {
        return SessionDatabaseHandler::selectAllByAttendee($idattendee);
    }

    /**
     * @param int $idevent
     * @return Session[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllByEvent(int $idevent): array
    {
        return SessionDatabaseHandler::selectAllByEvent($idevent);
    }

    /**
     * @param string $name
     * @param int $numberallowed
     * @param int $event
     * @param string $startdate
     * @param string $enddate
     * @return Session
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getNew(string $name, int $numberallowed, int $event, string $startdate, string $enddate): Session
    {
        return self::getById(SessionDatabaseHandler::insert($name, $numberallowed, $event, $startdate, $enddate));
    }
}