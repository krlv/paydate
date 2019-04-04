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
        $this->model = $paydateModel;

        foreach ($holidays as $date) {
            $this->holidays[$date] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePaydates(string $initialPaydate, int $numberOfPaydates): array
    {
        return [$initialPaydate];
    }

    /**
     * Checks whether $dateStr is a holiday
     *
     * @param string $dateStr
     * @return bool
     */
    public function isHoliday(string $dateStr): bool
	{
		$this->getDateImmutable($dateStr);
		return isset($this->holidays[$dateStr]);
	}

	/**
     * Checks whether $dateStr is a weekend
     *
     * @param string $dateStr
     * @return bool
     */
	public function isWeekend(string $dateStr): bool
	{
		$day = (int) $this->getDateImmutable($dateStr)->format($this::FORMAT_WEEKDAY);
		return $day === 0 || $day === 6;
	}

    /**
     * Returns DateTimeImmutable object for the date
     *
     * @param string $dateStr
     * @return \DateTimeImmutable
     * @throws \InvalidArgumentException
     */
	private function getDateImmutable(string $dateStr): \DateTimeImmutable
	{
		$date = \DateTimeImmutable::createFromFormat($this::FORMAT_DATE, $dateStr);

		if ($date === false) {
			throw new \InvalidArgumentException('Invalid date');
		}

		return $date;
	}
}
