<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/26/2019
 * Time: 11:45 AM
 */


namespace exceptions;


class SecurityException extends \Exception
{
    const USER_DOES_NOT_HAVE_PERMISSION = 201;
    const AUTHENTICATION_REQUIRED = 202;
    const USER_NOT_FOUND = 203;
    const PASSWORD_IS_INCORRECT = 204;
    const ACCOUNT_IS_DISABLED = 205;
}