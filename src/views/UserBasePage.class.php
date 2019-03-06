<?php
/**
 * ISTE 341
 * Project 1
 *
 * Lorenzo L. Romero
 *
 * User: lromero
 * Date: 2/27/2019
 * Time: 3:03 PM
 */


namespace views;


class UserBasePage extends View
{
    /**
     * UserBasePage constructor.
     * @throws \exceptions\DatabaseException
     * @throws \exceptions\EntryNotFoundException
     * @throws \exceptions\ViewException
     */
    public function __construct()
    {
        $header = new UserHeader();
        parent::setTemplateFromTemplate("HTMLDocument");
        parent::setVariable("header", $header->getHTML());
        parent::setVariable("footer", parent::templateFileContents("UserFooter"));
        parent::setVariable("content", parent::templateFileContents("UserBody"));
        parent::setVariable("siteTitle", FB_CONFIG['siteTitle']);

        // Display notice dialog
        if(isset($_GET['NOTICE']) OR isset($_GET['ERROR']))
        {
            parent::setVariable("notifications", parent::templateFileContents("Notifications"));

            if(isset($_GET['ERROR']))
            {
                parent::setVariable("notificationTitle", "Error");
                parent::setVariable("notifications", $_GET['ERROR']);
                parent::setVariable("notificationClass", "notifications-error");
            }
            else
            {
                parent::setVariable("notificationTitle", "Notice");
                parent::setVariable("notifications", $_GET['NOTICE']);
                parent::setVariable("notificationClass", "notifications-notice");
            }
        }
    }
}