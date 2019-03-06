<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 7:15 AM
 */


namespace exceptions;


class EntryNotFoundException extends \Exception
{
    const PRIMARY_KEY_NOT_FOUND = 101;
    const UNIQUE_KEY_NOT_FOUND = 102;
    const NO_RECORDS_FOUND = 103; // This should be used in cases where there is no result of a query based on another object
    // e.g No child records exist for a given parent
}