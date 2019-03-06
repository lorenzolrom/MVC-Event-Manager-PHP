<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 11:22 AM
 */


namespace models;


use database\TokenDatabaseHandler;
use exceptions\ErrorMessages;
use exceptions\TokenException;

class Token
{
    private $token;
    private $idattendee;
    private $issuedate;
    private $expiredate;
    private $ipaddress;
    private $expired;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getIdattendee(): int
    {
        return $this->idattendee;
    }

    /**
     * @return string
     */
    public function getIssuedate(): string
    {
        return $this->issuedate;
    }

    /**
     * @return string
     */
    public function getExpiredate(): string
    {
        return $this->expiredate;
    }

    /**
     * @return string
     */
    public function getIpaddress(): string
    {
        return $this->ipaddress;
    }

    /**
     * @return int
     */
    public function getExpired(): int
    {
        return $this->expired;
    }

    /**
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function markExpired(): bool
    {
        return TokenDatabaseHandler::setExpired($this->token, 1);
    }

    /**
     * @return bool
     * @throws TokenException
     * @throws \exceptions\DatabaseException
     */
    public function validate(): bool
    {
        $isAfterExpireTime = strtotime(date("Y-m-d H:i:s")) > strtotime($this->expiredate);

        if($isAfterExpireTime)
            $this->markExpired();

        if($this->expired == 1 OR $isAfterExpireTime)
        {
            setcookie(FB_CONFIG['cookieName'], "", time() -3600, FB_CONFIG['baseURI']);
            throw new TokenException(ErrorMessages::TOKEN_IS_EXPIRED, TokenException::TOKEN_IS_EXPIRED);
        }

        return TRUE;
    }
}