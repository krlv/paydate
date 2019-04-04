<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\Test;

use DevXyz\Challenge\Paydate\Calculator;
use DevXyz\Challenge\Paydate\CalculatorInterface;
use DevXyz\Challenge\Paydate\Test\Traits\VisibilityTrait;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    use VisibilityTrait;

    public function testConstructor(): void
    {
        $calculator  = new Calculator($model = CalculatorInterface::PAYDATE_MODEL_WEEKLY);
        $modelObject = $this->getPrivateProperty($calculator, 'model');
        $this->assertEquals($model, $modelObject->toNative());
    }

    /**
     * @param string $initialPaydate
     * @param int    $numberOfPaydates
     * @param array  $expected
     *
     * @dataProvider weekendProvider
     */
    public function testCalculatePaydates(string $initialPaydate, int $numberOfPaydates, array $expected): void
    {
        $calculator  = new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY, [
            '2019-01-01',
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
        $date       = \DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
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
        $date       = \DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
        $calculator = new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY);
        $this->assertEquals($expected, $calculator->isWeekend($date));
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
