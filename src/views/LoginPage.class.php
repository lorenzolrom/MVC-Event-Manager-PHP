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


namespace views;


class LoginPage extends View
{
    /**
     * Login constructor.
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        parent::setTemplateFromTemplate("HTMLDocument");
        parent::setVariable("content", View::templateFileContents("Login"));
        parent::setVariable("includes", View::templateFileContents("LoginIncludes"));
        parent::setVariable("pageTitle", "Login");
        parent::setVariable("siteTitle", FB_CONFIG['siteTitle']);
    }
}