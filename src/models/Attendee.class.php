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


namespace models;


use database\AttendeeDatabaseHandler;
use database\SessionDatabaseHandler;
use database\TokenDatabaseHandler;
use exceptions\EntryNotFoundException;
use exceptions\ErrorMessages;
use exceptions\SecurityException;
use exceptions\ValidationUtility;
use factories\AttendeeFactory;
use factories\RoleFactory;
use factories\SessionFactory;
use factories\TokenFactory;

class Attendee extends Model
{
    private $idattendee;
    private $name;
    private $password;
    private $role;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param string $password
     * @return bool
     * @throws SecurityException
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public function authenticate(string $password): bool
    {
        // Validate password
        $this->passwordIsCorrect($password);

        // Expire existing tokens
        TokenDatabaseHandler::expireAllUser($this->idattendee);

        // Create token
        $token = TokenFactory::getNewToken($this->idattendee);

        // Assign token as COOKIE
        setcookie(\FB_CONFIG['cookieName'], $token->getToken(), 0, FB_CONFIG['baseURI']);
        return TRUE;
    }

    /**
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function logout(): bool
    {
        TokenDatabaseHandler::expireAllUser($this->idattendee);
        setcookie(\FB_CONFIG['cookieName'], "", -3600, FB_CONFIG['baseURI']);
        return TRUE;
    }

    /**
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function registerForSession(int $idsession): bool
    {
        if($this->isRegisteredForSession($idsession))
            return FALSE;

        // Register user for session
        AttendeeDatabaseHandler::registerAttendeeToSession($this->idattendee, $idsession);
            return TRUE;
    }

    /**
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function unregisterFromSession(int $idsession): bool
    {
        if(!$this->isRegisteredForSession($idsession))
            return FALSE;

        AttendeeDatabaseHandler::unregisterAttendeeFromSession($this->idattendee, $idsession);
            return TRUE;
    }

    /**
     * @param int $idsession
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function isRegisteredForSession(int $idsession): bool
    {
        return in_array($this->idattendee, SessionDatabaseHandler::selectSessionAttendeeIDs($idsession));
    }

    /**
     * @return Session[]
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public function getRegisteredSessions(): array
    {
        return SessionFactory::getAllByAttendee($this->idattendee);
    }

    /**
     * @param string $password
     * @return bool
     * @throws SecurityException
     */
    public function passwordIsCorrect(string $password): bool
    {
        if(self::hashPassword($password) == $this->password)
            return TRUE;

        throw new SecurityException(ErrorMessages::PASSWORD_IS_INCORRECT, SecurityException::PASSWORD_IS_INCORRECT);
    }

    /**
     * @param string $password
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setPassword(string $password): bool
    {
        return AttendeeDatabaseHandler::updateAttendeePassword($this->idattendee, self::hashPassword($password));
    }

    /**
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function delete(): bool
    {
        // Cannot delete admin
        if($this->idattendee == 1) //yuck, but will work, especially if the db initialize script defined admin as an id of '1'
            return FALSE;

        return AttendeeDatabaseHandler::delete($this->idattendee);
    }

    /**
     * @param string $name
     * @throws \exceptions\DatabaseException
     */
    public function setName(string $name)
    {
        AttendeeDatabaseHandler::updateName($this->idattendee, $name);
    }

    /**
     * @param int $role
     * @throws \exceptions\DatabaseException
     */
    public function setRole(int $role)
    {
        AttendeeDatabaseHandler::updateRole($this->idattendee, $role);
    }

    /**
     * @param int $idevent
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function isAManager(int $idevent): bool
    {
        return AttendeeDatabaseHandler::isAnEventManager($this->idattendee, $idevent);
    }

    /**
     * @param string|null $name
     * @return int Validation status
     * @throws \exceptions\DatabaseException
     */
    public static function validateName(?string $name): int
    {
        // Name is not null
        if($name === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Name not already taken
        try
        {
           AttendeeFactory::getByName($name);
           return ValidationUtility::VALUE_ALREADY_TAKEN;
        }
        catch(EntryNotFoundException $e)
        {
            // Proceed
        }

        // Name greater than 0 characters
        if(strlen($name) < 1)
            return ValidationUtility::VALUE_TOO_SHORT;

        // Name less than or equal to 100 characters
        if(strlen($name) > 100)
            return ValidationUtility::VALUE_TOO_LONG;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param int|null $role
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function validateRole(?int $role): int
    {
        // Is not null
        if($role === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Role exists
        try
        {
            RoleFactory::getById($role);
            return ValidationUtility::VALUE_IS_OK;
        }
        catch(EntryNotFoundException $e)
        {
            return ValidationUtility::VALUE_IS_INVALID;
        }
    }

    /**
     * @param string|null $password
     * @return int
     */
    public static function validatePassword(?string $password): int
    {
        // Is not null
        if($password === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // At least 8 characters
        if(strlen($password) < 8)
            return ValidationUtility::VALUE_TOO_SHORT;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return hash('sha256', hash('sha256', $password));
    }
}