<?php

namespace internet\a3\Exception;

use Exception;

/**
 * Class ToolshedException
 *
 * Assignment 3
 * Authors
 * Kelly Pitts      09098321
 * Zhenliang Cai    17108093
 * Hengchen Qiu     15310634
 */
class ToolshedException extends Exception
{
    private $errorMessage;
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}