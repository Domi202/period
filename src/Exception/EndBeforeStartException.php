<?php
declare(strict_types=1);
namespace Kreemers\Period\Exception;

use Exception;

class EndBeforeStartException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Start must be before end';

    /**
     * @var int
     */
    protected $code = 1555929112;
}
