<?php

namespace Cheesy;

/**
 * Class Contact
 *
 * @package Cheesy
 */
class Contact
{
    /**
     * @var array
     */
    private $errors = array();

    /**
     * Returns an error message
     *
     * @param $message
     */
    public function error($message)
    {
        die(json_encode(array('success' => false, 'message' => $message)));
    }

    /**
     * Returns a success message
     *
     * @param $message
     */
    public function success($message)
    {
        die(json_encode(array('success' => true, 'message' => $message)));
    }

    /**
     * Adds an error message to the this->errors variable.
     *
     * @param $message
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Checks if the this->errors variable contains errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        if (count($this->errors) > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * Returns an HTML version of any errors found in the this->errors variable.
     *
     * @return string
     */
    public function getErrorsHtml()
    {
        $html = "<p><strong>There were errors with your submission</strong></p>";
        $html .= "<ul>";

        foreach($this->errors as $error)
        {
            $html .= "<li>$error</li>";
        }

        return "$html</ul>";
    }

    /**
     * Checks for a valid email format. Returns true if valid; false otherwise.
     *
     * @param $email
     * @return int
     */
    public function isValidEmail($email)
    {
        return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s\'"<>@,;]+\.+[a-z]{2,6}))$#si', $email);
    }
}