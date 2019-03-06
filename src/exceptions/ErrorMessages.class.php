<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 9:18 PM
 */


namespace exceptions;


class ErrorMessages
{
    const MUST_LOGIN = "Please Sign In";
    const PAGE_NOT_FOUND = "Failed To Parse URL";
    const PAGE_NO_PERMISSION = "You Do Not Have Permission To View This Page";
    const ROLE_NOT_FOUND = "Role Was Not Found";

    const USER_NOT_FOUND = "Username Or Password Is Incorrect";

    const PASSWORD_IS_INCORRECT = "Username Or Password Is Incorrect";
    const PERMISSION_NOT_FOUND = "Permission Was Not Found";

    const TOKEN_NOT_FOUND = "Token Not Found";
    const TOKEN_IS_EXPIRED = "Token Expired";

    const VIEW_NOT_FOUND = "View Not Found";
    const TEMPLATE_NOT_FOUND = "Template Not Found";

    const CONTROLLER_NOT_FOUND = "Controller Not Found";

    const DATABASE_FAILED_TO_CONNECT = "Could Not Connect To Database";
    const DATABASE_DIRECT_QUERY_FAILED = "Direct Query Failure";
    const DATABASE_PREPARED_QUERY_FAILED = "Prepared Query Failure";
    const DATABASE_TRANSACTION_START_FAILED = "Failed To Begin Transaction";
    const DATABASE_TRANSACTION_COMMIT_FAILED = "Failed To Commit Transaction";
    const DATABASE_TRANSACTION_ROLLBACK_FAILED = "Failed To Rollback Transaction";

    const ENTRY_NOT_FOUND = "Not Found";
}