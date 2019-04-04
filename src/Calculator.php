<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate;

use DevXyz\Challenge\Paydate\ValueObject\Model;

final class Calculator implements CalculatorInterface
{
    private const FORMAT_DATE     = 'Y-m-d';
    private const FORMAT_WEEKDAY  = 'w';
    private const FORMAT_TODAY    = 'today';
    private const FORMAT_INCR     = '+1 day';
    private const FORMAT_DECR     = '-1 day';

    /**
     * @var string
     */
    private $model;

    /**
     * @var \DateTimeImmutable
     */
    private $today;

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
        $this->today    = new \DateTimeImmutable($this::FORMAT_TODAY);
        $this->holidays = [];

        foreach ($holidays as $date) {
            $this->toDateTime($date);
            $this->holidays[$date] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePaydates(string $initialPaydate, int $numberOfPaydates): array
    {
        $date  = $this->toDateTime($initialPaydate);
        $dates = [$initialPaydate];

        for ($i = 0; $i < $numberOfPaydates - 1; ++$i) {
            $date    = $this->nextPaydate($date);
            $dates[] = $date->format($this::FORMAT_DATE);
        }

        return $dates;
    }

    /**
     * Returns next valid paydate starting from $date.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function nextPaydate(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $date = $date->modify($this->model->toUnits());

        if ($this->isHoliday($date)) {
            $date = $this->decreasePaydate($date);
        }

        if ($this->isWeekend($date)) {
            $date = $this->increasePaydate($date);
        }

        if ($this->isToday($date)) {
            $date = $this->increasePaydate($date);
        }

        return $date;
    }

    /**
     * Checks whether $date is a holiday.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isHoliday(\DateTimeImmutable $date): bool
    {
        return isset($this->holidays[$date->format($this::FORMAT_DATE)]);
    }

    /**
     * Checks whether $date is a weekend.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isWeekend(\DateTimeImmutable $date): bool
    {
        $day = (int) $date->format($this::FORMAT_WEEKDAY);
        return 0 === $day || 6 === $day;
    }

    /**
     * Checks whether $date is today.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isToday(\DateTimeImmutable $date): bool
    {
        return 0 === $this->today->diff($date)->d;
    }

    /**
     * Checks whether $date is valid paydate.
     *
     * @param \DateTimeImmutable $date
     *
     * @return bool
     */
    public function isValidPaydate(\DateTimeImmutable $date): bool
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
    public function increasePaydate(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $date = $date->modify($this::FORMAT_INCR);
        return $this->isValidPaydate($date) ? $date : $this->increasePaydate($date);
    }

    /**
     * Decreases date by day(s) to the next valid paydate.
     *
     * @param \DateTimeImmutable $date
     *
     * @return \DateTimeImmutable
     */
    public function decreasePaydate(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $date = $date->modify($this::FORMAT_DECR);
        return $this->isValidPaydate($date) ? $date : $this->decreasePaydate($date);
    }

    /**
     * Converts $dateStr to DateTimeImmutable object.
     *
     * @param string $dateStr
     *
     * @throws \InvalidArgumentException
     *
     * @return \DateTimeImmutable
     */
    private function toDateTime(string $dateStr): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat($this::FORMAT_DATE, $dateStr);

        if (false === $date) {
            throw new \InvalidArgumentException('Invalid date');
        }

        return $date;
    }
}
