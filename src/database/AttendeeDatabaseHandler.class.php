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


use exceptions\DatabaseException;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use models\Attendee;

class AttendeeDatabaseHandler
{
    /**
     * @param int $idattendee
     * @return Attendee
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectById(int $idattendee): Attendee
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idattendee, name, password, role FROM attendee WHERE idattendee = ? LIMIT 1");
        $select->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Attendee");
    }

    /**
     * @param string $name
     * @return Attendee
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectByName(string $name): Attendee
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idattendee FROM attendee WHERE name = ? LIMIT 1");
        $select->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::UNIQUE_KEY_NOT_FOUND);

        return self::selectById($select->fetchColumn());
    }

    /**
     * @return Attendee[]
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAll(): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->query("SELECT idattendee FROM attendee");

        $handler->close();

        $attendees = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idattendee)
        {
            $attendees[] = self::selectById($idattendee);
        }

        return $attendees;
    }

    /**
     * @param string $name
     * @param string $password
     * @param int $role
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function insert(string $name, string $password, int $role): int
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO attendee (name, password, role) VALUES (:name, :password, :role)");
        $insert->bindParam('name', $name, DatabaseConnection::PARAM_STR);
        $insert->bindParam('password', $password, DatabaseConnection::PARAM_STR);
        $insert->bindParam('role', $role, DatabaseConnection::PARAM_INT);
        $insert->execute();

        $idroles = $handler->getLastInsertId();

        $handler->close();

        return $idroles;
    }

    /**
     * @param int $idattendee
     * @param string $name
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateName(int $idattendee, string $name): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE attendee SET name = ? WHERE idattendee = ?");
        $update->bindParam(1, $name, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idattendee, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @param string $password
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updatePassword(int $idattendee, string $password): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE attendee SET password = ? WHERE idattendee = ?");
        $update->bindParam(2, $idattendee, DatabaseConnection::PARAM_INT);
        $update->bindParam(1, $password, DatabaseConnection::PARAM_STR);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @param int $role
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateRole(int $idattendee, int $role): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE attendee SET role = ? WHERE idattendee = ?");
        $update->bindParam(1, $role, DatabaseConnection::PARAM_INT);
        $update->bindParam(2, $idattendee, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function delete(int $idattendee): bool
    {
        $handler = new DatabaseConnection();

        $delete = $handler->prepare("DELETE FROM attendee WHERE idattendee = ?");
        $delete->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $deleteRegistrations = $handler->prepare("DELETE FROM attendee_session WHERE attendee = ?");
        $deleteRegistrations->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $deleteRegistrations->execute();

        $deleteManage = $handler->prepare("DELETE FROM manger_event WHERE manager = ?");
        $deleteManage->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $deleteManage->execute();

        $handler->close();

        return $delete->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function registerAttendeeToSession(int $idattendee, int $idsession): bool
    {
        $handler = new DatabaseConnection();

        $insert = $handler->prepare("INSERT INTO attendee_session (session, attendee) VALUES (:session, :attendee)");
        $insert->bindParam('session', $idsession, DatabaseConnection::PARAM_INT);
        $insert->bindParam('attendee', $idattendee, DatabaseConnection::PARAM_INT);
        $insert->execute();

        $handler->close();

        return $insert->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function unregisterAttendeeFromSession(int $idattendee, int $idsession): bool
    {
        $handler = new DatabaseConnection();

        $delete = $handler->prepare("DELETE FROM attendee_session WHERE session = :session AND attendee = :attendee");
        $delete->bindParam('session', $idsession, DatabaseConnection::PARAM_INT);
        $delete->bindParam('attendee', $idattendee, DatabaseConnection::PARAM_INT);
        $delete->execute();

        $handler->close();

        return $delete->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @param string $password
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function updateAttendeePassword(int $idattendee, string $password): bool
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE attendee SET password = ? WHERE idattendee = ?");
        $update->bindParam(1, $password, DatabaseConnection::PARAM_STR);
        $update->bindParam(2, $idattendee, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount() === 1;
    }

    /**
     * @param int $idevent
     * @return Attendee[]
     * @throws DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAllManagingEvent(int $idevent): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT manager FROM manger_event WHERE event = ?");
        $select->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $managers = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idattendee)
        {
            $managers[] = self::selectById($idattendee);
        }

        return $managers;
    }

    /**
     * @param int $idsession
     * @return Attendee[]
     * @throws DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAllRegisteredForSession(int $idsession): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT attendee FROM attendee_session WHERE session = ?");
        $select->bindParam(1, $idsession, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        $attendees = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idattendee)
        {
            $attendees[] = self::selectById($idattendee);
        }

        return $attendees;
    }

    /**
     * @param int $idattendee
     * @param int $idevent
     * @return bool
     * @throws DatabaseException
     */
    public static function isAnEventManager(int $idattendee, int $idevent): bool
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT manager FROM manger_event WHERE event = ? AND manager = ? LIMIT 1");
        $select->bindParam(1, $idevent, DatabaseConnection::PARAM_INT);
        $select->bindParam(2, $idattendee, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        return $select->getRowCount() === 1;
    }
}