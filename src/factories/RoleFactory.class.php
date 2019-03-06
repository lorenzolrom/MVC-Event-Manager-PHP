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


use database\RoleDatabaseHandler;
use models\Role;

class RoleFactory
{
    /**
     * @param int $idroles
     * @return Role
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getById(int $idroles): Role
    {
        return RoleDatabaseHandler::selectById($idroles);
    }

    /**
     * @return Role[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getAll(): array
    {
        return RoleDatabaseHandler::selectAll();
    }
}