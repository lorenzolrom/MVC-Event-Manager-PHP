<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 11:25 AM
 */


namespace database;


use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use models\Token;

class TokenDatabaseHandler
{
    /**
     * @param string $token
     * @return Token
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public static function selectByToken(string $token): Token
    {
        $handler = new DatabaseConnection();

        $select = $handler->prepare("SELECT token, idattendee, issuedate, expiredate, ipaddress, expired FROM token WHERE token = ? LIMIT 1");
        $select->bindParam(1, $token, DatabaseConnection::PARAM_STR);
        $select->execute();

        $handler->close();

        if($select->getRowCount() !== 1)
            throw new EntryNotFoundException(ErrorMessages::ENTRY_NOT_FOUND, EntryNotFoundException::PRIMARY_KEY_NOT_FOUND);

        return $select->fetchObject("models\Token");
    }

    /**
     * @param int $idattendee User to create token for
     * @return string Newly created token
     * @throws \exceptions\DatabaseException
     */
    public static function insert(int $idattendee): string
    {
        $handler = new DatabaseConnection();

        $token = hash('sha512', openssl_random_pseudo_bytes(2048));

        $insert = $handler->prepare("INSERT into token (token, idattendee, issuedate, expiredate, ipaddress) 
                VALUES (:token, :idattendee, NOW(), NOW() + INTERVAL 1 HOUR, :ipaddress)");

        $insert->bindParam('token', $token, DatabaseConnection::PARAM_STR);
        $insert->bindParam('idattendee', $idattendee, DatabaseConnection::PARAM_INT);
        $insert->bindParam(':ipaddress', $_SERVER['REMOTE_ADDR'], DatabaseConnection::PARAM_STR);
        $insert->execute();

        $handler->close();

        return $token;
    }

    /**
     * @param string $token
     * @param int $expired
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public static function setExpired(string $token, int $expired): bool
    {
        $handler = new DatabaseConnection();

        $fetch = $handler->prepare("UPDATE token SET expired = ? WHERE token = ?");
        $fetch->bindParam(1, $expired, DatabaseConnection::PARAM_INT);
        $fetch->bindParam(2, $token, DatabaseConnection::PARAM_STR);
        $fetch->execute();

        $handler->close();

        return $fetch->getRowCount() === 1;
    }

    /**
     * @param int $idattendee
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function expireAllUser(int $idattendee): int
    {
        $handler = new DatabaseConnection();

        $update = $handler->prepare("UPDATE token SET expired = 1 WHERE idattendee = ?");
        $update->bindParam(1, $idattendee, DatabaseConnection::PARAM_INT);
        $update->execute();

        $handler->close();

        return $update->getRowCount();
    }
}