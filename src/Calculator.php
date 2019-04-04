<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate;

use DevXyz\Challenge\Paydate\ValueObject\Model;
use DevXyz\Challenge\Paydate\ValueObject\Paydate;

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
        $this->model    = Model::fromNative($paydateModel);
        $this->holidays = [];

        foreach ($holidays as $date) {
            Paydate::fromNative($date);
            $this->holidays[$date] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePaydates(string $initialPaydate, int $numberOfPaydates): array
    {
        $date  = Paydate::fromNative($initialPaydate);
        $dates = [$initialPaydate];

        for ($i = 0; $i < $numberOfPaydates - 1; ++$i) {
            $date = $date->modify($this->model->toUnits());

            if ($this->isHoliday($date)) {
                $dates[] = (string) $this->decreasePaydate($date);
                continue;
            }

            if ($this->isWeekend($date)) {
                $dates[] = (string) $this->increasePaydate($date);
                continue;
            }

            if ($this->isToday($date)) {
                $dates[] = (string) $this->increasePaydate($date);
                continue;
            }

            $dates[] = (string) $date;
        }

        return $dates;
    }

    /**
     * Checks whether $date is a holiday.
     *
     * @param Paydate $date
     *
     * @return bool
     */
    public function isHoliday(Paydate $date): bool
    {
        return isset($this->holidays[(string) $date]);
    }

    /**
     * Checks whether $date is a weekend.
     *
     * @param Paydate $date
     *
     * @return bool
     */
    public function isWeekend(Paydate $date): bool
    {
        return 0 === $date->getWeekday() || 6 === $date->getWeekday();
    }

    /**
     * Checks whether $date is today.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isToday(Paydate $date): bool
    {
        return $date->equals(Paydate::today());
    }

    /**
     * Checks whether $date is valid paydate.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isValidPaydate(Paydate $date): bool
    {
        return !($this->isWeekend($date) || $this->isHoliday($date) || $this->isToday($date));
    }

    /**
     * Increases date by day(s) to the next valid paydate.
     *
     * @param \DateTimeImmutable $date
     *
     * @return \DateTimeImmutable
     */
    public function increasePaydate(Paydate $date): Paydate
    {
        do {
            $date = $date->increment();
        } while (!$this->isValidPaydate($date));

        return $date;
    }

    /**
     * Decreases date by day(s) to the next valid paydate.
     *
     * @param \DateTimeImmutable $date
     *
     * @return \DateTimeImmutable
     */
    public function decreasePaydate(Paydate $date): Paydate
    {
        do {
            $date = $date->decrement();
        } while (!$this->isValidPaydate($date));

        return $date;
    }
}
