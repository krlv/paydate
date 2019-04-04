<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\ValueObject;

final class Paydate implements ValueObjectInterface
{
    private const FORMAT_DATE     = 'Y-m-d';
    private const FORMAT_TODAY    = 'today';
    private const FORMAT_WEEKDAY  = 'w';

    private const MODIFY_MIDNIGHT = 'midnight';
    private const MODIFY_INCR     = '+1 day';
    private const MODIFY_DECR     = '-1 day';

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @param string             $model
     * @param \DateTimeImmutable $date
     */
    public function __construct(\DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function __toString(): string
    {
        return $this->toNative();
    }

    public static function today()
    {
        $date = new \DateTimeImmutable(static::FORMAT_TODAY);
        return new static($date);
    }

    public static function fromNative($native)
    {
        $date = \DateTimeImmutable::createFromFormat(static::FORMAT_DATE, $native);

        if (false === $date) {
            throw new \InvalidArgumentException('Invalid date');
        }

        return new static($date->modify(static::MODIFY_MIDNIGHT));
    }

    public function equals(ValueObjectInterface $vo): bool
    {
        if (\get_class($vo) != \get_class($this)) {
            return false;
        }

        return $this->date == $vo->date;
    }

    public function toNative()
    {
        return $this->date->format($this::FORMAT_DATE);
    }

    public function getWeekday(): int
    {
        return (int) $this->date->format($this::FORMAT_WEEKDAY);
    }

    public function modify(string $modify): self
    {
        $paydate       = clone $this;
        $paydate->date = $this->date->modify($modify);
        return $paydate;
    }

    public function increment(): self
    {
        return $this->modify($this::MODIFY_INCR);
    }

    public function decrement(): self
    {
        return $this->modify($this::MODIFY_DECR);
    }
}
