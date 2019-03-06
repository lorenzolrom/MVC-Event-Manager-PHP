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


use database\EventDatabaseHandler;
use exceptions\EntryNotFoundException;
use exceptions\ValidationUtility;
use factories\AttendeeFactory;
use factories\EventFactory;
use factories\VenueFactory;

class Event extends Model
{
    private $idevent;
    private $name;
    private $datestart;
    private $dateend;
    private $numberallowed;
    private $venue;

    /**
     * @return int
     */
    public function getIdevent(): int
    {
        return $this->idevent;
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
    public function getDatestart(): string
    {
        return $this->datestart;
    }

    /**
     * @return string
     */
    public function getDateend(): string
    {
        return $this->dateend;
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
    public function getVenue(): int
    {
        return $this->venue;
    }

    /**
     * @return bool
     * @throws EntryNotFoundException
     * @throws \exceptions\DatabaseException
     */
    public function delete(): bool
    {
        return EventDatabaseHandler::delete($this->idevent);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setName(string $name): bool
    {
        return EventDatabaseHandler::updateName($this->idevent, $name);
    }

    /**
     * @param string $datestart
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setDatestart(string $datestart): bool
    {
        return EventDatabaseHandler::updateDatestart($this->idevent, $datestart);
    }

    /**
     * @param string $dateend
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setDateend(string $dateend): bool
    {
        return EventDatabaseHandler::updateDateend($this->idevent, $dateend);
    }

    /**
     * @param int $numberallowed
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setNumberallowed(int $numberallowed): bool
    {
        return EventDatabaseHandler::updateNumberallowed($this->idevent, $numberallowed);
    }

    /**
     * @param int $venue
     * @return bool
     * @throws \exceptions\DatabaseException
     */
    public function setVenue(int $venue): bool
    {
        return EventDatabaseHandler::updateVenue($this->idevent, $venue);
    }

    /**
     * @param int[] $managers
     * @throws \exceptions\DatabaseException
     */
    public function setManagers(array $managers)
    {
        EventDatabaseHandler::removeAllManagers($this->idevent); // shortcuts....

        foreach($managers as $idattendee)
        {
            EventDatabaseHandler::addManager($this->idevent, intval($idattendee));
        }
    }

    /**
     * @param string|null $name
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function validateName(?string $name): int
    {
        // Not null
        if($name === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Does not already exist
        try
        {
            EventFactory::getByName($name);
            return ValidationUtility::VALUE_ALREADY_TAKEN;
        }
        catch(EntryNotFoundException $e){}

        // At least 1 character
        if(strlen($name) < 1)
            return ValidationUtility::VALUE_TOO_SHORT;

        // Not greater than 50 characters
        if(strlen($name) > 50)
            return ValidationUtility::VALUE_TOO_LONG;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param string|null $date
     * @return int
     */
    public static function validateDateX(?string $date): int
    {
        // Not null
        if($date === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Is valid date
        if(!ValidationUtility::validDate($date))
            return ValidationUtility::VALUE_IS_INVALID;

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
     * @param int|null $venue
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function validateVenue(?int $venue): int
    {
        // Not null
        if($venue === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Exists
        try
        {
            VenueFactory::getById($venue);
            return ValidationUtility::VALUE_IS_OK;
        }
        catch (EntryNotFoundException $e)
        {
            return ValidationUtility::VALUE_IS_INVALID;
        }
    }

    /**
     * @param array|null $managers
     * @return int
     * @throws \exceptions\DatabaseException
     */
    public static function validateManagers(?array $managers): int
    {
        if($managers === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        foreach($managers as $idattendee)
        {
            try
            {
                AttendeeFactory::getById(intval($idattendee));
            }
            catch(EntryNotFoundException $e)
            {
                return ValidationUtility::VALUE_IS_INVALID;
            }
        }

        return ValidationUtility::VALUE_IS_OK;
    }
}