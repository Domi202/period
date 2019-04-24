<?php
declare(strict_types=1);
namespace Kreemers\Period;

use DateTime;

interface Period
{
    public function getStart(): DateTime;

    public function getEnd(): DateTime;

    public function equals(Period $period): bool;

    public function in(Period $period): bool;

    public function encloses(Period $period): bool;

    public function intersects(Period $period): bool;
}