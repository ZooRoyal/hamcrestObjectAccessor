<?php

declare(strict_types=1);

use Hamcrest\Matcher;
use SebastianKnott\HamcrestObjectAccessor\HasProperty;

if (!function_exists('hasProperty')) {
    /**
     * This Matcher tries to access a property of an object by name. It uses
     * Getters, public properties, hassers and issers.
     *
     * @param string $propertyName
     * @param mixed|Matcher $propertyValueMatcher
     *
     * @return HasProperty
     */
    function hasProperty($propertyName, $propertyValueMatcher)
    {
        return HasProperty::hasProperty($propertyName, $propertyValueMatcher);
    }
}
