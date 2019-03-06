<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/25/2019
 * Time: 7:43 PM
 */


namespace models;


class Role
{
    private $idroles;
    private $name;

    /**
     * @return int
     */
    public function getIdroles(): int
    {
        return $this->idroles;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}