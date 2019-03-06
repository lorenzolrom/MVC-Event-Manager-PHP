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
use factories\AttendeeFactory;
use models\Attendee;
use models\Session;

class SessionDatabaseHandler
{
    /**
     * @param int $idsession
     * @return Session
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectById(int $idsession): Session
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idsession, name, numberallowed, event, startdate, enddate FROM session WHERE idsession = ? LIMIT 1");
        $select->bindParam(1, $idsession, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Session");
    }

    /**
     * @param int $idevent
     * @return Session[]
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectByEvent(int $idevent): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idsession FROM session WHERE event = ? ORDER BY startdate ASC");
        $select->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $sessions = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idsession)
        {
            $sessions[] = self::selectById($idsession);
        }

        return $sessions;
    }

    /**
     * @param int $idsession
     * @return Attendee[]
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectSessionAttendees(int $idsession): array
    {
        $attendees = array();

        foreach(self::selectSessionAttendeeIDs($idsession) as $idattendee)
        {
            $attendees[] = AttendeeFactory::getById($idattendee);
        }

        return $attendees;
    }

    /**
     * @param int $idsession
     * @return array
     * @throws \exceptions\DatabaseException
     */
    public static function selectSessionAttendeeIDs(int $idsession)
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT attendee FROM attendee_session WHERE session = ?");
        $select->bindParam(1, $idsession, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        return $select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0);
    }

    /**
     * @param int $idattendee
     * @return Session[]
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAllByAttendee(int $idattendee): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT session FROM attendee_session WHERE attendee = ?");
        $select->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $sessions = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idsession)
        {
            $sessions[] = self::selectById($idsession);
        }

        return $sessions;
    }

    /**
     * @param int $idevent
     * @return array
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectAllByEvent(int $idevent): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idsession FROM session WHERE event = ?");
        $select->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $sessions = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idsession)
        {
            $sessions[] = self::selectById($idsession);
        }

        return $sessions;
    }

    /**
     * @param string $name
     * @param int $numberallowed
     * @param int $event
     * @param string $startdate
     * @param string $enddate
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function insert(string $name, int $numberallowed, int $event, string $startdate, string $enddate): int
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO session (name, numberallowed, event, startdate, enddate) 
            VALUES (:name, :numberallowed, :event, :startdate, :enddate)");
        $insert->bindParam('name', $name, DatabaseConnection::PARAM_STR);
        $insert->bindParam('numberallowed', $numberallowed, DatabaseConnection::PARAM_INT);
        $insert->bindParam('event', $event, DatabaseConnection::PARAM_INT);
        $insert->bindParam('startdate', $startdate, DatabaseConnection::PARAM_STR);
        $insert->bindParam('enddate', $enddate, DatabaseConnection::PARAM_STR);
        $insert->execute();
        $idsession = $handler->getLastInsertId();

        $handler->close();

        return $idsession;
    }

    /**
     * @param int $idsession
     * @param string $name
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateName(int $idsession, string $name): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE session SET name = ? WHERE idsession = ?");
        $update->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idsession, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idsession
     * @param int $numberallowed
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateNumberAllowed(int $idsession, int $numberallowed)
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE session SET numberallowed = ? WHERE idsession = ?");
        $update->bindParam(1, $numberallowed, DatabaseConnection::PARAM_INT);
        $update->bindParam(2, $idsession, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idsession
     * @param string $statdate
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateStartdate(int $idsession, string $statdate)
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE session SET startdate = ? WHERE idsession = ?");
        $update->bindParam(1, $statdate, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idsession, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idsession
     * @param string $enddate
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateEnddate(int $idsession, string $enddate)
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE session SET enddate = ? WHERE idsession = ?");
        $update->bindParam(1, $enddate, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idsession, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function delete(int $idsession): bool
    {
        $handler = new DatabaseConnection();

        // delete registrations
        $deleteRegistrations = $handler->prepare("DELETE FROM attendee_session WHERE session = ?");
        $deleteRegistrations->bindParam(1, $idsession, DatabaseConnection::PARAM_INT);
        $deleteRegistrations->execute();

        // delete session
        $delete = $handler->prepare("DELETE FROM session WHERE idsession = ?");
        $delete->bindParam(1, $idsession, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $handler->close();
        return $delete->getRowCount() === 1;
    }
}