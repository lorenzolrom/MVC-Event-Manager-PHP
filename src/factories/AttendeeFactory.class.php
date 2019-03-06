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


namespace factories;


use database\AttendeeDatabaseHandler;
use models\Attendee;

class AttendeeFactory
{
    /**
     * @param int $idattendee
     * @return Attendee
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getById(int $idattendee): Attendee
    {
        return AttendeeDatabaseHandler::selectById($idattendee);
    }

    /**
     * @param string $name
     * @return Attendee
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getByName(string $name): Attendee
    {
        return AttendeeDatabaseHandler::selectByName($name);
    }

    /**
     * @return Attendee[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAll(): array
    {
        return AttendeeDatabaseHandler::selectAll();
    }

    /**
     * @param int $idevent
     * @return Attendee[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllManagingEvent(int $idevent): array
    {
        return AttendeeDatabaseHandler::selectAllManagingEvent($idevent);
    }

    /**
     * @param int $idevent
     * @return Attendee[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAllRegisteredForEvent(int $idevent): array
    {
        return AttendeeDatabaseHandler::selectAllRegisteredForSession($idevent);
    }

    /**
     * @param string $name
     * @param string $password
     * @param int $role
     * @return Attendee
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getNew(string $name, string $password, int $role): Attendee
    {
        return AttendeeDatabaseHandler::selectById(AttendeeDatabaseHandler::insert($name, Attendee::hashPassword($password), $role));
    }
}