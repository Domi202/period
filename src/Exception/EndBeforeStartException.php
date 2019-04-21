<?php
declare(strict_types=1);
namespace Kreemers\Period\Exception;

use Exception;
use Throwable;

class EndBeforeStartException extends Exception
{
    public function __construct(
        $message = "Start must be before end",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}