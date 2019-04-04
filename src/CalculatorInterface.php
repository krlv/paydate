<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate;

interface CalculatorInterface
{
    public const PAYDATE_MODEL_MONTHLY  = 'MONTHLY';
    public const PAYDATE_MODEL_BIWEEKLY = 'BIWEEKLY';
    public const PAYDATE_MODEL_WEEKLY   = 'WEEKLY';

    /**
     * @param string   $paydateModel One of the paydate model options MONTHLY|BIWEEKLY|WEEKLY
     * @param string[] $holidays     List of holidays; make sure to provide holidays here
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $paydateModel, array $holidays = []);

    /**
     * Takes a paydate model and first paydate and generates the next $numberOfPaydates paydates.
     *
     * @param string $initialPaydate   First paydate as a string in Y-m-d format
     * @param int    $numberOfPaydates The number of paydates to generate
     *
     * @throws \InvalidArgumentException
     *
     * @return string[] The next paydates (from today) as strings in Y-m-d format
     */
    public function calculatePaydates(string $initialPaydate, int $numberOfPaydates): array;
}
