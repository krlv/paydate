<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\Test;

use DevXyz\Challenge\Paydate\Calculator;
use DevXyz\Challenge\Paydate\CalculatorInterface;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function testCalculatePaydates(): void
    {
        new Calculator(CalculatorInterface::PAYDATE_MODEL_WEEKLY);
    }
}
