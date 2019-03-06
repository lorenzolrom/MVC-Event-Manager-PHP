<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 11:27 AM
 */


namespace factories;


use database\TokenDatabaseHandler;
use models\Token;

class TokenFactory
{
    /**
     * @param string $token
     * @return Token
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getByToken(string $token): Token
    {
        return TokenDatabaseHandler::selectByToken($token);
    }

    /**
     * @param int $idattendee
     * @return Token
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public static function getNewToken(int $idattendee): Token
    {
        return self::getByToken(TokenDatabaseHandler::insert($idattendee));
    }
}