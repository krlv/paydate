<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\ValueObject;

use DevXyz\Challenge\Paydate\CalculatorInterface;

final class Model implements ValueObjectInterface
{
    private const UNITS_MONTHLY     = '1 month';
    private const UNITS_BIWEEKLY    = '2 weeks';
    private const UNITS_WEEKLY      = '1 week';

    /**
     * @var string
     */
    private $model;

    /**
     * @param string $model
     */
    public function __construct(string $model)
    {
        switch ($model) {
            case CalculatorInterface::PAYDATE_MODEL_MONTHLY:
            case CalculatorInterface::PAYDATE_MODEL_BIWEEKLY:
            case CalculatorInterface::PAYDATE_MODEL_WEEKLY:
                $this->model = $model;
                break;

            default:
                throw new \InvalidArgumentException('Invalid Payday Model');
                break;
        }
    }

    public static function fromNative($native)
    {
        return new static((string) $native);
    }

    public function equals(ValueObjectInterface $vo): bool
    {
        if (\get_class($vo) != \get_class($this)) {
            return false;
        }

        return $this->model == $vo->model;
    }

    public function toNative()
    {
        return $this->model;
    }

    public function toUnits(): string
    {
        switch ($this->model) {
            case CalculatorInterface::PAYDATE_MODEL_MONTHLY:
                $units = $this::UNITS_MONTHLY;
                break;

            case CalculatorInterface::PAYDATE_MODEL_BIWEEKLY:
                $units = $this::UNITS_BIWEEKLY;
                break;

            case CalculatorInterface::PAYDATE_MODEL_WEEKLY:
                $units = $this::UNITS_WEEKLY;
                break;

            default:
                throw new \InvalidArgumentException('Invalid Payday Model');
                break;
        }

        return $units;
    }
}
