<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TimeCalculatorTest extends TestCase
{

    #[DataProvider('timeSpanProvider')]
    public function testCalculateSpanInMonth($start, $end, $expected)
    {
        $calculator = new TimeCalculator();

        $span = $calculator->calculateTimePeriods($start, $end, 0);
        $this->assertEquals($expected, count($span));
    }

    public static function timeSpanProvider()
    {
        return [
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                12
            ],
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2016-07-02'),
                1
            ],
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2016-08-01'),
                2
            ],
            [
                new \DateTime('2016-08-01'),
                new \DateTime('2016-07-01'),
                2
            ]
        ];
    }

    #[DataProvider('lastDayProvider')]
    public function testGetLastDayInMonth($date, $expected)
    {
        $calculator = new TimeCalculator();
        $lastDay = $calculator->getLastDayInMonth($date);
        $this->assertEquals($expected, $lastDay);
    }

    public static function lastDayProvider()
    {
        return [
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2016-07-31')
            ],
            [
                new \DateTime('2016-07-05'),
                new \DateTime('2016-07-31')
            ],
            [
                new \DateTime('2016-07-31'),
                new \DateTime('2016-07-31')
            ],
            [
                new \DateTime('2016-02-10'),
                new \DateTime('2016-02-29')
            ],
            [
                new \DateTime('2016-12-01'),
                new \DateTime('2016-12-31')
            ]
        ];
    }
}
