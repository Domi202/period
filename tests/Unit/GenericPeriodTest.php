<?php
namespace Kreemers\Period\Tests\Unit;

use DateTime;
use Kreemers\Period\Exception\EndBeforeStartException;
use Kreemers\Period\GenericPeriod;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Kreemers\Period\GenericPeriod
 * @covers ::<!public>
 */
class GenericPeriodTest extends TestCase
{
    /**
     * @covers ::create
     * @covers ::__construct
     * @covers ::getStart
     * @covers ::getEnd
     */
    public function test_create()
    {
        $start = new DateTime('2019-04-16 12:00');
        $end = new DateTime('2019-05-16 12:00');

        $subject = GenericPeriod::create(
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

    /**
     * @covers ::createDay
     */
    public function test_createDay()
    {
        $day = new DateTime('2019-05-04');

        $subject = GenericPeriod::createDay($day);

        $this->assertEquals(
            new DateTime('2019-05-04 00:00:00'),
            $subject->getStart()
        );
        $this->assertEquals(
            new DateTime('2019-05-04 23:59:59'),
            $subject->getEnd()
        );
    }

    /**
     * @covers ::createMonth
     */
    public function test_createMonth()
    {
        $month = new DateTime('2019-05-12');

        $subject = GenericPeriod::createMonth($month);

        $this->assertEquals(
            new DateTime('2019-05-01 00:00:00'),
            $subject->getStart(),
            'Start does not match'
        );
        $this->assertEquals(
            new DateTime('2019-05-31 23:59:59'),
            $subject->getEnd(),
            'End does not match'
        );
    }

    /**
     * @covers ::create
     */
    public function test_throwExceptionOnInvalidCreate()
    {
        $start = new DateTime('2019-04-17 12:00');
        $end = new DateTime('2019-04-16 12:00');

        $this->expectException(EndBeforeStartException::class);
        $this->expectExceptionCode(1555929112);

        GenericPeriod::create(
            $start,
            $end
        );
    }

    /**
     * @return array
     */
    public function equals_dataProvider(): array
    {
        return [
            'equals' => [
                'subject' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'notEquals' => [
                'subject' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-18 12:00'),
                    new DateTime('2019-04-19 18:00')
                ),
                'assertedResult' => false,
            ],
            'diffTimezones' => [
                'subject' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00', new \DateTimeZone('Europe/Berlin')),
                    new DateTime('2019-04-16 18:00', new \DateTimeZone('Europe/Berlin'))
                ),
                'reference' => GenericPeriod::create(
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
     * @param GenericPeriod $subject
     * @param GenericPeriod $reference
     * @param bool $assertedResult
     */
    public function test_equals(GenericPeriod $subject, GenericPeriod $reference, bool $assertedResult)
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
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-15 9:00'),
                    new DateTime('2019-04-17 15:00')
                ),
                'assertedResult' => true,
            ],
            'is_not_in' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 7:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => GenericPeriod::create(
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
     * @param GenericPeriod $subject
     * @param GenericPeriod $reference
     * @param bool $assertedResult
     */
    public function test_in(GenericPeriod $subject, GenericPeriod $reference, bool $assertedResult)
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
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-15 9:00'),
                    new DateTime('2019-04-17 15:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'assertedResult' => true,
            ],
            'not_encloses' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 7:00'),
                    new DateTime('2019-04-16 15:00')
                ),
                'reference' => GenericPeriod::create(
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
     * @param GenericPeriod $subject
     * @param GenericPeriod $reference
     * @param bool $assertedResult
     */
    public function test_encloses(GenericPeriod $subject, GenericPeriod $reference, bool $assertedResult)
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
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 13:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'intersects_end' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 17:30'),
                    new DateTime('2019-04-16 20:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => true,
            ],
            'does_not_intersect' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 11:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-16 12:00'),
                    new DateTime('2019-04-16 18:00')
                ),
                'assertedResult' => false,
            ],
            'period_is_in_reference' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-16 9:00'),
                    new DateTime('2019-04-16 11:00')
                ),
                'reference' => GenericPeriod::create(
                    new DateTime('2019-04-15 12:00'),
                    new DateTime('2019-04-17 18:00')
                ),
                'assertedResult' => true,
            ],
            'reference_is_in_period' => [
                'period' => GenericPeriod::create(
                    new DateTime('2019-04-15 12:00'),
                    new DateTime('2019-04-17 18:00')
                ),
                'reference' => GenericPeriod::create(
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
     * @param GenericPeriod $period
     * @param GenericPeriod $reference
     * @param bool $assertedResult
     */
    public function test_intersects(GenericPeriod $period, GenericPeriod $reference, bool $assertedResult)
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

    private function periodToString(GenericPeriod $period): string
    {
        return sprintf(
            '[Period %s | %s]',
            $period->getStart()->format(DateTime::W3C),
            $period->getEnd()->format(DateTime::W3C)
        );
    }
}
