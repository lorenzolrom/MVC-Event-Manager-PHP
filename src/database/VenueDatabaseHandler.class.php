<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:43 PM
 */


namespace database;


use exceptions\DatabaseException;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use factories\EventFactory;
use factories\VenueFactory;
use models\Venue;

class VenueDatabaseHandler
{
    /**
     * @param int $idvenue
     * @return Venue
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectById(int $idvenue): Venue
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idvenue, name, capacity FROM venue WHERE idvenue = ? LIMIT 1");
        $select->bindParam(1, $idvenue, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Venue");
    }

    /**
     * @return Venue[]
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectAll(): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->query("SELECT idvenue FROM venue");

        $handler->close();

        $venues = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idvenue)
        {
            $venues[] = self::selectById($idvenue);
        }

        return $venues;
    }

    /**
     * @param int $idvenue
     * @param string $name
     * @return bool
     * @throws DatabaseException
     */
    public static function updateName(int $idvenue, string $name): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE venue SET name = ? WHERE idvenue = ?");
        $update->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idvenue, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idvenue
     * @param int $capacity
     * @return bool
     * @throws DatabaseException
     */
    public static function updateCapacity(int $idvenue, int $capacity): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE venue SET capacity = ? WHERE idvenue = ?");
        $update->bindParam(1, $capacity, DatabaseConnection::PARAM_INT);
        $update->bindParam(2, $idvenue, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param string $name
     * @param int $capacity
     * @return int
     * @throws DatabaseException
     */
    public static function insert(string $name, int $capacity): int
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO venue (name, capacity) VALUES (:name, :capacity)");
        $insert->bindParam('name', $name, DatabaseConnection::PARAM_STR);
        $insert->bindParam('capacity', $capacity, DatabaseConnection::PARAM_INT);
        $insert->execute();
        $idvenue = $handler->getLastInsertId();

        $handler->close();

        return $idvenue;
    }

    /**
     * @param int $idvenue
     * @return bool
     * @throws DatabaseException
     * @throws EntryNotFoundException
     */
    public static function delete(int $idvenue): bool
    {
        $handler = new DatabaseConnection();

        // Delete events associated with this venue
        foreach(EventFactory::getAllByVenue($idvenue) as $event)
        {
            $event->delete();
        }

        // Delete venue
        $delete = $handler->prepare("DELETE FROM venue WHERE idvenue = ?");
        $delete->bindParam(1, $idvenue, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $handler->close();

        return $delete->getRowCount() === 1;
    }
}