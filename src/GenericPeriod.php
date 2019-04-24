<?php
declare(strict_types=1);
namespace Kreemers\Period;

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
        $this->start = clone $start;
        $this->end = clone $end;
    }

    public static function create(
        DateTime $start,
        DateTime $end
    ) {
        if ($start > $end) {
            throw new EndBeforeStartException();
        }

        return new static(
            $start,
            $end
        );
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