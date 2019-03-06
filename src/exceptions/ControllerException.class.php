<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 9:07 PM
 */


namespace exceptions;


class ControllerException extends \Exception
{
    const CONTROLLER_NOT_FOUND = 501;
}