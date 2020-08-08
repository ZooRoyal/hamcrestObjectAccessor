<?php

declare(strict_types=1);

namespace SebastianKnott\HamcrestObjectAccessor;

use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\Matchers;
use Hamcrest\TypeSafeDiagnosingMatcher;
use Hamcrest\TypeSafeMatcher;
use Hamcrest\Util;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * This Matcher tries to access a property of an object by name. It uses
 * Getters, public properties, hassers and issers.
 */
class HasProperty extends TypeSafeDiagnosingMatcher
{
    /** @var PropertyAccessor */
    private $accessor;

    /** @var string */
    private $propertyName;

    /** @var Matcher */
    private $propertyValueMatcher;

    /**
     * HasProperty constructor.
     *
     * @param string  $propertyName
     * @param Matcher $propertyValueMatcher
     */
    public function __construct($propertyName, Matcher $propertyValueMatcher)
    {
        parent::__construct(TypeSafeMatcher::TYPE_OBJECT);

        if (!Matchers::typeOf('string')->matches($propertyName)) {
            throw new InvalidArgumentException(
                'Property name must be string.',
                1596896381
            );
        }

        $this->propertyName         = $propertyName;
        $this->propertyValueMatcher = $propertyValueMatcher;
        $this->accessor             = new PropertyAccessor(true);
    }

    /**
     * Generates a description of the object. The description may be part
     * of a description of a larger object of which this is just a component,
     * so it should be worded appropriately.
     *
     * @param Description $description
     *
     * @return void
     */
    public function describeTo(Description $description)
    {
        $description->appendText(
            'an object with public property "' . $this->propertyName
            . '" with a value matching '
        );
        $this->propertyValueMatcher->describeTo($description);
    }

    /**
     * Subclasses should implement these. The item will already have been checked for
     * the specific type.
     *
     * @param mixed       $item
     * @param Description $mismatchDescription
     *
     * @return bool|null
     */
    protected function matchesSafelyWithDiagnosticDescription($item, Description $mismatchDescription)
    {
        $propertyValue = null;
        try {
            $propertyValue = $this->accessor->getValue($item, $this->propertyName);
            $this->propertyValueMatcher->describeMismatch($propertyValue, $mismatchDescription);
        } catch (NoSuchPropertyException $exception) {
            $exceptionDescription = $exception->getMessage();
            $mismatchDescription->appendText(lcfirst(rtrim($exceptionDescription, '.')) . ' ');
        }

        return $this->propertyValueMatcher->matches($propertyValue);
    }

    /**
     * Static factory method.
     *
     * @param string $propertyName
     * @param mixed  $propertyValueMatcher
     *
     * @return HasProperty
     */
    public static function hasProperty($propertyName, $propertyValueMatcher)
    {
        return new self($propertyName, Util::wrapValueWithIsEqual($propertyValueMatcher));
    }
}
