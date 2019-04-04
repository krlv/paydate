<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\Test;

use DevXyz\Challenge\Paydate\Calculator;
use DevXyz\Challenge\Paydate\CalculatorInterface;
use DevXyz\Challenge\Paydate\Test\Traits\VisibilityTrait;
use DevXyz\Challenge\Paydate\ValueObject\Paydate;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    use VisibilityTrait;

    public function testConstructor(): void
    {
        $calculator  = new Calculator($model = 'WEEKLY');
        $modelObject = $this->getPrivateProperty($calculator, 'model');
        $this->assertEquals($model, $modelObject->toNative());
    }

    /**
     * @param string $initialPaydate
     * @param int    $numberOfPaydates
     * @param array  $expected
     *
     * @dataProvider paydatesProvider
     */
    public function testCalculatePaydates(string $initialPaydate, int $numberOfPaydates, array $expected): void
    {
        $calculator  = new Calculator('MONTHLY', [
            '2019-01-01',
            '2019-11-11',
            '2019-12-25',
        ]);
        $this->assertEquals($expected, $calculator->calculatePaydates($initialPaydate, $numberOfPaydates));
    }

    /**
     * @param string $dateStr
     * @param bool   $expected
     *
     * @dataProvider holidayProvider
     */
    public function testIsHoliday(string $dateStr, bool $expected): void
    {
        $date       = Paydate::fromNative($dateStr);
        $calculator = new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY, ['2019-01-01', '2019-12-25']);
        $this->assertEquals($expected, $calculator->isHoliday($date));
    }

    /**
     * @param string $dateStr
     * @param bool   $expected
     *
     * @dataProvider weekendProvider
     */
    public function testIsWeekend(string $dateStr, bool $expected): void
    {
        $date       = Paydate::fromNative($dateStr);
        $calculator = new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY);
        $this->assertEquals($expected, $calculator->isWeekend($date));
    }

    public function paydatesProvider(): array
    {
        // TBD
        $today   = new \DateTimeImmutable();
        $fstDate = $today->modify('-1 month');
        $sndDate = $today->modify('+1 day');
        $trdDate = $today->modify('+1 months');

        return [
            // case 1: monthly model, not today, no weekends, no holidays
            ['2019-04-03', 3, ['2019-04-03', '2019-05-03', '2019-06-03']],

            // case 2: monthly model, not today, with weekends and holidays
            // 2nd paydate will be 2019-12-24 (2019-12-25 is Christmas day => decrease the date to 2019-12-24)
            // 3rd paydate will be 2020-01-25 (2019-12-25 is Saturday      => increase the date to Monday, 27th)
            ['2019-11-25', 3, ['2019-11-25', '2019-12-24', '2020-01-27']],

            // case 3: monthly model, not today, payday on weekends, monday is holidays
            // 2nd paydate will be 2019-11-12:
            // 1. 2019-11-09 is Saturday      => increase the date to Monday, 11th
            // 2. Monday 11th is Veterans Day => increase the date to Tuesday, 12th
            // (not decreasing on holiday, 'cause holiday is the result of inital weekend's increment)
            ['2019-10-09', 3, ['2019-10-09', '2019-11-12', '2019-12-09']],

            // case 4: monthly model, paydate is today
            // TBD
        ];
    }

    public function holidayProvider(): array
    {
        return [
            ['2019-01-01', true],
            ['2019-04-01', false],
            ['2019-04-06', false],
            ['2019-12-25', true],
        ];
    }

    public function weekendProvider(): array
    {
        return [
            ['2019-04-01', false],
            ['2019-04-05', false],
            ['2019-04-06', true],
            ['2019-04-07', true],
        ];
    }
}
