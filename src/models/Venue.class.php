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


use database\VenueDatabaseHandler;
use exceptions\ValidationUtility;

class Venue extends Model
{
    private $idvenue;
    private $name;
    private $capacity;

    /**
     * @return int
     */
    public function getIdvenue(): int
    {
        return $this->idvenue;
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
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * @param string $name
     * @throws \exceptions\DatabaseException
     */
    public function setName(string $name)
    {
        VenueDatabaseHandler::updateName($this->idvenue, $name);
    }

    /**
     * @param int $capacity
     * @throws \exceptions\DatabaseException
     */
    public function setCapacity(int $capacity)
    {
        VenueDatabaseHandler::updateCapacity($this->idvenue, $capacity);
    }

    /**
     * @return bool
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     */
    public function delete(): bool
    {
        return VenueDatabaseHandler::delete($this->idvenue);
    }

    /**
     * @param string|null $name
     * @return int
     */
    public static function validateName(?string $name): int
    {
        // Is not null
        if($name === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Is not less than 1 character
        if(strlen($name) < 1)
            return ValidationUtility::VALUE_TOO_SHORT;

        // Is not greater than 50 characters
        if(strlen($name) > 50)
            return ValidationUtility::VALUE_TOO_LONG;

        return ValidationUtility::VALUE_IS_OK;
    }

    /**
     * @param int|null $capacity
     * @return int
     */
    public static function validateCapacity(?int $capacity): int
    {
        // Not null
        if($capacity === NULL)
            return ValidationUtility::VALUE_IS_NULL;

        // Is greater than 0
        if($capacity < 0)
            return ValidationUtility::VALUE_IS_INVALID;

        return ValidationUtility::VALUE_IS_OK;
    }
}