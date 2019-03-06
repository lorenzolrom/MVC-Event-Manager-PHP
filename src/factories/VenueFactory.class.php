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


use database\VenueDatabaseHandler;
use models\Venue;

class VenueFactory
{
    /**
     * @param int $idvenue
     * @return Venue
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getById(int $idvenue): Venue
    {
        return VenueDatabaseHandler::selectById($idvenue);
    }

    /**
     * @return Venue[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAll(): array
    {
        return VenueDatabaseHandler::selectAll();
    }

    /**
     * @param string $name
     * @param int $capacity
     * @return Venue
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getNew(string $name, int $capacity): Venue
    {
        return self::getById(VenueDatabaseHandler::insert($name, $capacity));
    }
}