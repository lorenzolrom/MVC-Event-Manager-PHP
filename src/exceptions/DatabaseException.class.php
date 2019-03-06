<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:49 PM
 */


namespace exceptions;


class DatabaseException extends \Exception
{
    const FAILED_TO_CONNECT = 1;
    const DIRECT_QUERY_FAILED = 2;
    const PREPARED_QUERY_FAILED = 3;
    const TRANSACTION_START_FAILED = 4;
    const TRANSACTION_COMMIT_FAILED = 5;
    const TRANSACTION_ROLLBACK_FAILED = 6;
}