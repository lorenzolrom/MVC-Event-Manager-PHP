<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * Configuration File
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:35 PM
 */

// Trying something new....config as array
const FB_CONFIG = [
    'siteTitle' => 'ISTE Event Manager',
    'baseURL' => 'my.domain',
    'baseURI' => '/',
    'defaultPage' => 'myaccount',

    'cookieName' => 'ISTE_EM_COOKIE',

    'databaseHost' => 'my.database.server',
    'databaseUser' => 'user',
    'databasePassword' => 'password',
    'databaseName' => 'database name',

    'roles' => ['admin', 'event manager', 'attendee']
];