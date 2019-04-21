<?php
namespace Kreemers\Period\Tests\Unit;

use DateTime;
use Kreemers\Period\Period;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Kreemers\Period\Period
 * @covers ::<!public>
 */
class PeriodTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getStart
     * @covers ::getEnd
     */
    public function test_construct()
    {
        $start = new DateTime('2019-04-16 12:00');
        $end = new DateTime('2019-05-16 12:00');

        $subject = Period::create(
            $start,
            $end
        );

        $this->assertEquals(
            $start,
            $subject->getStart()
        );
        $this->assertNotSame(
            $start,
            $subject->getStart()
        );

        $this->assertEquals(
            $end,
            $subject->getEnd()
        );
        $this->assertNotSame(
            $end,
            $subject->getEnd()
        );
    }

    protected function periodToString(Period $period): string
    {
        return sprintf(
            '[Period %s | %s]',
            $period->getStart()->format(DateTime::W3C),
            $period->getEnd()->format(DateTime::W3C)
        );
    }

    /**
     * @return array
     */
    public function equals_dataProvider(): array
    {
        return [
            'equals' => [
                'subject' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'notEquals' => [
                'subject' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-18 12:00'),
                    new DateTime('2019-04-19 18:00')
                ),
                'assertedResult' => false,
            ],
            'diffTimezones' => [
                'subject' => Period::create(
                    new DateTime('2019-04-16 12:00', new \DateTimeZone('Europe/Berlin')),
                    new DateTime('2019-04-16 18:00', new \DateTimeZone('Europe/Berlin'))
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 12:00', new \DateTimeZone('UTC')),
                    new DateTime('2019-04-16 18:00', new \DateTimeZone('UTC'))
                ),
                'assertedResult' => false,
            ],
        ];
    }

    /**
     * @dataProvider equals_dataProvider
     * @covers ::equals
     *
     * @param Period $subject
     * @param Period $reference
     * @param bool $assertedResult
     */
    public function test_equals(Period $subject, Period $reference, bool $assertedResult)
    {
        $result = $subject->equals($reference);

        $this->assertEquals(
            $assertedResult,
            $result
        );
    }

    /**
     * @return array
     */
    public function in_dataProvider(): array
    {
        return [
            'is_in' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-15 9:00'),
                    new DateTime('2019-04-17 15:00')
                ),
                'assertedResult' => true,
            ],
            'is_not_in' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 7:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 19:00')
                ),
                'assertedResult' => false,
            ],
        ];
    }

    /**
     * @dataProvider in_dataProvider
     * @covers ::in
     * @param Period $subject
     * @param Period $reference
     * @param bool $assertedResult
     */
    public function test_in(Period $subject, Period $reference, bool $assertedResult)
    {
        $result = $subject->in($reference);

        $this->assertEquals(
            $assertedResult,
            $result,
            sprintf(
                '%s is not in %s',
                $this->periodToString($subject),
                $this->periodToString($reference)
            )
        );
    }

    /**
     * @return array
     */
    public function encloses_dataProvider(): array
    {
        return [
            'encloses' => [
                'period' => Period::create(
                    new DateTime('2019-04-15 9:00'),
                    new DateTime('2019-04-17 15:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'assertedResult' => true,
            ],
            'not_encloses' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 7:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 19:00')
                ),
                'assertedResult' => false,
            ],
        ];
    }

    /**
     * @dataProvider encloses_dataProvider
     * @covers ::encloses
     * @param Period $subject
     * @param Period $reference
     * @param bool $assertedResult
     */
    public function test_encloses(Period $subject, Period $reference, bool $assertedResult)
    {
        $result = $subject->encloses($reference);

        $this->assertEquals(
            $assertedResult,
            $result,
            sprintf(
                '%s does not enclose %s',
                $this->periodToString($subject),
                $this->periodToString($reference)
            )
        );
    }

    /**
     * @return array
     */
    public function intersects_dataProvider(): array
    {
        return [
            'intersects_start' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 13:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'intersects_end' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 17:30'),
                    new DateTime('2019-04-16 20:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'does_not_intersect' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 11:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => false,
            ],
            'period_is_in_reference' => [
                'period' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 11:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-15 12:00'),
                    new DateTime('2019-04-17 18:00')
                ),
                'assertedResult' => true,
            ],
            'reference_is_in_period' => [
                'period' => Period::create(
                    new DateTime('2019-04-15 12:00'),
                    new DateTime('2019-04-17 18:00')
                ),
                'reference' => Period::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 11:00')
                ),
                'assertedResult' => true,
            ],
        ];
    }

    /**
     * @dataProvider intersects_dataProvider
     * @covers ::intersects
     *
     * @param Period $period
     * @param Period $reference
     * @param bool $assertedResult
     */
    public function test_intersects(Period $period, Period $reference, bool $assertedResult)
    {
        $result = $period->intersects($reference);

        $this->assertEquals(
            $assertedResult,
            $result,
            sprintf(
                '%s does not intersect with %s',
                $this->periodToString($period),
                $this->periodToString($reference)
            )
        );
    }
}
