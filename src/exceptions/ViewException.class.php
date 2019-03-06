<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:49 PM
 */


namespace exceptions;


class ViewException extends \Exception
{
    const VIEW_NOT_FOUND = 401;
    const TEMPLATE_NOT_FOUND = 402;
}