<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 12:47 PM
 */


namespace exceptions;


class ValidationUtility
{
    const VALUE_IS_OK = 0;
    const VALUE_IS_NULL = 1;
    const VALUE_ALREADY_TAKEN = 2;
    const VALUE_TOO_SHORT = 3;
    const VALUE_TOO_LONG = 4;
    const VALUE_IS_INVALID = 5;

    /**
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validDate(string $date, string $format = "Y-m-d"): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * @param $value
     * @param null|mixed $ifNull
     * @return null
     */
    public static function ifNull($value, $ifNull = NULL)
    {
        if(isset($value))
            return $value;
        return $ifNull;
    }
}