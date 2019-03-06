<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 8:45 PM
 */


namespace controllers;


class RegistrationController extends Controller
{

    public function getPage(string $uri): string
    {
        return "registration";
    }
}