<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 3/03/2019
 * Time: 9:59 AM
 */


namespace views;


class ChangePasswordPage extends View
{
    /**
     * ChangePasswordPage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $page = new UserBasePage();
        parent::setTemplate($page->getTemplate());
        parent::setVariable("pageTitle", "Change Password");
        parent::setVariable("content", self::templateFileContents("ChangePassword"));
    }
}