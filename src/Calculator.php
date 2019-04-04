<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate;

final class Calculator implements CalculatorInterface
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var string[]
     */
    private $holidays;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $paydateModel, array $holidays = [])
    {
        $this->model    = $paydateModel;
        $this->holidays = $holidays;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePaydates(string $initialPaydate, int $numberOfPaydates): array
    {
        return [];
    }
}
