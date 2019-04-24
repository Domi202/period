<?php
declare(strict_types=1);
namespace Kreemers\Period;

use DateInterval;
use DateTime;
use Kreemers\Period\Exception\EndBeforeStartException;

final class GenericPeriod implements Period
{
    /**
     * @var DateTime
     */
    private $start;

    /**
     * @var DateTime
     */
    private $end;

    private function __construct(
        DateTime $start,
        DateTime $end
    ) {
        if ($start > $end) {
            throw new EndBeforeStartException();
        }

        $this->start = clone $start;
        $this->end = clone $end;
    }

    public static function create(
        DateTime $start,
        DateTime $end
    ) {
        return new static(
            $start,
            $end
        );
    }

    public static function createDay(
        DateTime $day
    ) {
        $start = (clone $day)->setTime(0, 0, 0);
        $end = (clone $day)->setTime(23, 59, 59);
        return new static($start, $end);
    }

    public static function createMonth(
        DateTime $month
    ) {
        $start = (clone $month)->sub(
            DateInterval::createFromDateString(((int) $month->format('d') - 1) . ' days')
        );
        $end = (clone $start)->add(
            DateInterval::createFromDateString(((int) $month->format('t') - 1) . ' days')
        )->setTime(23, 59, 59);
        return new static($start, $end);
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function equals(Period $period): bool
    {
        return $this->getStart() == $period->getStart()
            && $this->getEnd() == $period->getEnd();
    }

    public function in(Period $period): bool
    {
        if ($this->getStart() >= $period->getStart()
            && $this->getStart() <= $period->getEnd()
            && $this->getEnd() >= $period->getStart()
            && $this->getEnd() <= $period->getEnd()
        ) {
            return true;
        }

        return false;
    }

    public function encloses(Period $period): bool
    {
        if ($this->getStart() <= $period->getStart()
            && $this->getEnd() >= $period->getEnd()
        ) {
            return true;
        }

        return false;
    }

    public function intersects(Period $period): bool
    {
        if ($this->getStart() >= $period->getStart()
            && $this->getStart() <= $period->getEnd()
        ) {
            return true;
        }

        if ($this->getEnd() <= $period->getEnd()
            && $this->getEnd() >= $period->getStart()
        ) {
            return true;
        }

        if ($this->in($period)
            || $this->encloses($period)
        ) {
            return true;
        }

        return false;
    }
}