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

        $calculator = new Calculator($model = CalculatorInterface::PAYDATE_MODEL_WEEKLY, $holidays = ['2019-01-01']);
        $this->assertEquals($holidays, $this->getPrivateProperty($calculator, 'holidays'));
    }
}
