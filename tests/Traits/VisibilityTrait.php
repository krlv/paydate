<?php

declare(strict_types=1);

namespace DevXyz\Challenge\Paydate\Test\Traits;

trait VisibilityTrait
{
    /**
     * Set private property to an object.
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected function setPrivateProperty(object $object, string $property, $value): void
    {
        \Closure::bind(static function ($object, $value) use ($property): void {
            $object->$property = $value;
        }, null, $object)->__invoke($object, $value);
    }

    /**
     * Get private property from an object.
     *
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    protected function getPrivateProperty(object $object, string $property)
    {
        return \Closure::bind(static function ($object) use ($property) {
            return $object->$property;
        }, null, $object)->__invoke($object);
    }
}
