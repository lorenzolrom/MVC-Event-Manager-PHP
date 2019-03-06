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


use exceptions\ErrorMessages;
use exceptions\ViewException;

abstract class View
{
    protected $template;

    /**
     * @param string $variable
     * @param $value
     */
    public function setVariable(string $variable, $value)
    {
        $this->template = str_replace("{{@$variable}}", $value, $this->template);
    }

    /**
     * Removes existing template variables and returns template content
     * @return string
     * @throws ViewException
     */
    public function getHTML(): string
    {
        $this->setVariable("includes", self::templateFileContents("Includes"));
        $this->setVariable("baseURI", FB_CONFIG['baseURI']);
        return preg_replace("/\{\{@(.*)\}\}/", null, $this->template);
    }

    /**
     * Returns raw template content without replacing variables
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param array $errors
     * @throws ViewException
     */
    public function setErrors(array $errors)
    {
        $errorMessages = "<ul>";

        foreach($errors as $error)
        {
            $errorMessages .= "<li>$error</li>";
        }

        $errorMessages .= "</ul>";

        $this->setVariable("notifications", View::templateFileContents("Notifications"));
        $this->setVariable("notificationClass", "notifications-error");
        $this->setVariable("notificationTitle", "Error");
        $this->setVariable("notifications", $errorMessages);
    }

    /**
     * @param string $template
     * @throws ViewException
     */
    protected function setTemplateFromTemplate(string $template)
    {
        $this->template = self::templateFileContents($template);
    }

    /**
     * Manually set the template contents
     * @param string $contents
     */
    protected function setTemplate(string $contents)
    {
        $this->template = $contents;
    }


    /**
     * @param string $template
     * @return string
     * @throws ViewException
     */
    public static function templateFileContents(string $template): string
    {
        $file = dirname(__FILE__) . "/templates/$template.html";

        if(!is_file($file))
            throw new ViewException(ErrorMessages::VIEW_NOT_FOUND, ViewException::TEMPLATE_NOT_FOUND);

        return file_get_contents($file);
    }
}