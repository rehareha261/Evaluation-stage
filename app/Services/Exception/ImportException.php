<?php

namespace App\Services\Exception;

use Exception;

class ImportException extends Exception
{
    protected $details;

    public function __construct(string $message = "", array $details = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

?>
