<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\ValueObject;

interface ValueObjectInterface
{
    /**
     * @param mixed $native
     *
     * @return ValueObjectInterface
     */
    public static function fromNative($native);

    /**
     * @param ValueObjectInterface $vo
     *
     * @return bool
     */
    public function equals(self $vo): bool;

    /**
     * @return mixed
     */
    public function toNative();
}
