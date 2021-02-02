<?php

namespace PayzeIO\LaravelPayze\Exceptions;

use Exception;

class UnsupportedLanguageException extends Exception
{
    /**
     * UnsupportedLanguageException constructor.
     *
     * @param string $language
     */
    public function __construct(string $language)
    {
        parent::__construct();

        $this->message = sprintf('Unsupported Language "%s"', $language);
    }
}
