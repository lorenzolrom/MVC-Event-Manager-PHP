<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:14 PM
 */


namespace controllers;


abstract class Controller
{
    abstract public function getPage(string $uri): string;
}