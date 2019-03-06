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
use models\Role;

class RoleDatabaseHandler
{
    /**
     * @param int $idroles
     * @return Role
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectById(int $idroles): Role
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT idroles, name FROM roles WHERE idroles = ? LIMIT 1");
        $select->bindParam(1, $idroles, DatabaseConnection::PARAM_INT);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Role");
    }

    /**
     * @return Role[]
     * @throws \exceptions\DatabaseException
     * @throws EntryNotFoundException
     */
    public static function selectAll(): array
    {
        $handler = new DatabaseConnection();

        $select = $handler->query("SELECT idroles FROM roles");

        $handler->close();

        $roles = array();

        foreach($select->fetchAll(DatabaseConnection::FETCH_COLUMN, 0) as $idroles)
        {
            $roles[] = self::selectById($idroles);
        }

        return $roles;
    }
}