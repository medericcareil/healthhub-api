<?php

namespace App\Kernel\Failure;

/**
 * Class Failure
 * @package App\Kernel\Failure
 */
abstract class Failure
{
    /** 
     * @var string
    */
    private string $message;

    /**
     * Failure constructor.
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
