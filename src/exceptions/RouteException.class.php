<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:18 PM
 */


namespace exceptions;


class RouteException extends \Exception
{
    const ROUTE_NOT_SUPPLIED = 401;
    const ROUTE_URI_NOT_FOUND = 402;
    const REQUIRED_PARAMETER_MISSING = 403;
    const REQUIRED_PARAMETER_IS_INVALID = 404;
    const REQUIRED_DOCUMENT_PARAMETER_MISSING = 405;
}