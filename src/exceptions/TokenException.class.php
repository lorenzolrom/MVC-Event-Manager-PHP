<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 11:35 AM
 */


namespace exceptions;


class TokenException extends \Exception
{
    const TOKEN_IS_EXPIRED = 301;
    const TOKEN_NOT_FOUND = 302;
}