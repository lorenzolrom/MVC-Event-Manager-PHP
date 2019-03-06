<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * AIO Index Page
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:38 PM
 */

// Include configuration file
require_once (dirname(__FILE__) . "/../config.php");

// Register Class Inclusion Script
spl_autoload_register(
    function($className)
    {
        /** @noinspection PhpIncludeInspection */
        require_once("../" . str_replace("\\", "/", $className) . ".class.php");
    }
);

echo \controllers\FrontController::getPage();