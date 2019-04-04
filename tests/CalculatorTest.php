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

    public function testCalculatePaydates(): void
    {
        $calculator = new Calculator($model = CalculatorInterface::PAYDATE_MODEL_WEEKLY);
        $this->assertEquals($model, $this->getPrivateProperty($calculator, 'model'));
        $this->assertEmpty($this->getPrivateProperty($calculator, 'holidays'));

        $calculator = new Calculator($model = CalculatorInterface::PAYDATE_MODEL_WEEKLY, ['2019-01-01']);
        $this->assertEquals(['2019-01-01' => true], $this->getPrivateProperty($calculator, 'holidays'));
    }

    /**
     * @param string $date
     * @param bool   $expected
     *
     * @dataProvider holidayProvider
     */
	public function testIsHoliday(string $date, bool $expected): void
	{
		$calculator = new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY, ['2019-01-01', '2019-12-25']);
		$this->assertEquals($expected, $calculator->isHoliday($date));
	}

	/**
     * @param string $date
     * @param bool   $expected
     *
     * @dataProvider weekendProvider
     */
	public function testIsWeekend(string $date, bool $expected): void
	{
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
