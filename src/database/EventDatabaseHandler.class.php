<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:42 PM
 */


namespace database;


use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use factories\SessionFactory;
use models\Event;

class EventDatabaseHandler
{
    /**
     * @param int $idevent
     * @return Event
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectById(int $idevent): Event
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idevent, name, datestart, dateend, numberallowed, venue FROM event WHERE idevent = ? LIMIT 1");
        $select->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Event");
    }

    /**
     * @param string $name
     * @return Event
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectByName(string $name): Event
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idevent FROM event WHERE name = ? LIMIT 1");
        $select->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::UNIQUE_KEY_NOT_FOUND);

        return self::selectById($select->fetchColumn());
    }

    /**
     * @return Event[]
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectAll(): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->query("SELECT idevent FROM event ORDER BY dateend ASC");

        $handler->close();

        $events = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idevent)
        {
            $events[] = self::selectById($idevent);
        }

        return $events;
    }

    /**
     * @param int $idattendee
     * @return Event[]
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAllFromManager(int $idattendee): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT event FROM manger_event WHERE manager = ?");
        $select->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $events = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idevent)
        {
            $events[] = self::selectById($idevent);
        }

        return $events;
    }

    /**
     * @param int $idvenue
     * @return array
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAllFromVenue(int $idvenue): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idevent FROM event WHERE venue = ?");
        $select->bindParam(1, $idvenue, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $venues = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idevent)
        {
            $venues[] = self::selectById($idevent);
        }

        return $venues;
    }

    /**
     * @param string $name
     * @param string $datestart
     * @param string $dateend
     * @param int $numberallowed
     * @param int $venue
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function insert(string $name, string $datestart, string $dateend, int $numberallowed, int $venue): int
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO event (name, datestart, dateend, numberallowed, venue) VALUES 
                                                              (:name, :datestart, :dateend, :numberallowed, :venue)");
        $insert->bindParam('name', $name, DatabaseConnection::PARAM_STR);
        $insert->bindParam('datestart', $datestart, DatabaseConnection::PARAM_STR);
        $insert->bindParam('dateend', $dateend, DatabaseConnection::PARAM_STR);
        $insert->bindParam('numberallowed', $numberallowed, DatabaseConnection::PARAM_INT);
        $insert->bindParam('venue', $venue, DatabaseConnection::PARAM_INT);
        $insert->execute();

        $idvenue = $handler->getLastInsertId();

        $handler->close();

        return $idvenue;
    }

    /**
     * @param int $idevent
     * @return bool
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function delete(int $idevent): bool
    {
        $handler = new DatabaseConnection();

        // Delete sessions
        foreach(SessionFactory::getAllByEvent($idevent) as $session)
        {
            $session->delete();
        }

        // Delete managers
        self::removeAllManagers($idevent);

        // Delete event
        $delete = $handler->prepare("DELETE FROM event WHERE idevent = ?");
        $delete->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $handler->close();

        return $delete->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param string $name
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateName(int $idevent, string $name): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE event SET name = ? WHERE idevent = ?");
        $update->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idevent, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param string $datestart
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateDatestart(int $idevent, string $datestart): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE event SET datestart = ? WHERE idevent = ?");
        $update->bindParam(1, $datestart, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idevent, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param string $dateend
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateDateend(int $idevent, string $dateend): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE event SET dateend = ? WHERE idevent = ?");
        $update->bindParam(1, $dateend, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idevent, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param int $numberallowed
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateNumberallowed(int $idevent, int $numberallowed): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE event SET numberallowed = ? WHERE idevent = ?");
        $update->bindParam(1, $numberallowed, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idevent, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param int $idvenue
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateVenue(int $idevent, int $idvenue): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE event SET venue = ? WHERE idevent = ?");
        $update->bindParam(1, $idvenue, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idevent, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @param int $idattendee
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function addManager(int $idevent, int $idattendee): bool
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO manger_event (manager, event)VALUES (:manager, :event)");
        $insert->bindParam('event', $idevent, DatabaseConnection::PARAM_INT);
        $insert->bindParam('manager', $idattendee, DatabaseConnection::PARAM_STR);
        $insert->execute();

        $handler->close();

        return $insert->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function removeAllManagers(int $idevent): bool // that shortcut though...
    {
        $handler = new DatabaseConnection();

        $delete = $handler->prepare("DELETE FROM manger_event WHERE event = ?");
        $delete->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $handler->close();

        return $delete->getRowCount() !== 0;
    }
}