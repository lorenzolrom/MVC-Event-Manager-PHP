<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:44 PM
 */


namespace models;


use database\SessionDatabaseHandler;
use exceptions\EntryNotFoundException;
use exceptions\ValidationUtility;
use factories\AttendeeFactory;
use factories\EventFactory;

class Session extends Model
{
    private $idsession;
    private $name;
    private $numberallowed;
    private $event;
    private $startdate;
    private $enddate;

    /**
     * @return int
     */
    public function getIdsession(): int
    {
        return $this->idsession;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNumberallowed(): int
    {
        return $this->numberallowed;
    }

    /**
     * @return int
     */
    public function getEvent(): int
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getStartdate(): string
    {
        return $this->startdate;
    }

    /**
     * @return string
     */
    public function getEnddate(): string
    {
        return $this->enddate;
    }

    /**
     * @return int
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public function getRegisterCount(): int
    {
        return sizeof(SessionDatabaseHandler::selectSessionAttendees($this->idsession));
    }

    /**
     * @return Attendee[]
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public function getAttendees(): array
    {
        return AttendeeFactory::getAllRegisteredForEvent($this->idsession);
    }

    /**
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function delete(): bool
    {
        return SessionDatabaseHandler::delete($this->idsession);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setName(string $name):bool
    {
        return SessionDatabaseHandler::updateName($this->idsession, $name);
    }

    /**
     * @param int $numberallowed
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setNumberallowed(int $numberallowed): bool
    {
        return SessionDatabaseHandler::updateNumberAllowed($this->idsession, $numberallowed);
    }

    /**
     * @param string $startdate
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setStartdate(string $startdate): bool
    {
        return SessionDatabaseHandler::updateStartdate($this->idsession, $startdate);
    }

    /**
     * @param string $enddate
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setEnddate(string $enddate): bool
    {
        return SessionDatabaseHandler::updateEnddate($this->idsession, $enddate);
    }

    /**
     * @param string|null $name
     * @return int
     */
    public static function validateName(?string $name): int
    {
        // Not null
        if($name === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // At least 1 character
        if(strlen($name) < 1)
            return ValidationUtility::VALUE_TOO_SHORT;

        // Not greater than 50 characters
        if(strlen($name) > 50)
            return ValidationUtility::VALUE_TOO_LONG;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param int|null $numberallowed
     * @return int
     */
    public static function validateNumberAllowed(?int $numberallowed): int
    {
        // Not null
        if($numberallowed === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Is greater than 0
        if(intval($numberallowed) < 0)
            return ValidationUtility::VALUE_IS_INVALID;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param string|null $date
     * @return int
     */
    public static function validateXDate(?string $date): int
    {
        // Not null
        if($date === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Is valid date
        if(!ValidationUtility::validDate($date, 'Y-m-d H:i:s'))
            return ValidationUtility::VALUE_IS_INVALID;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param int|null $event
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function validateEvent(?int $event): int
    {
        // Not null
        if ($event === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Exists
        try
        {
            EventFactory::getById($event);
            return ValidationUtility::VALUE_IS_OK;
        }
        catch (EntryNotFoundException $e)
        {
            return ValidationUtility::VALUE_IS_INVALID;
        }
    }
}