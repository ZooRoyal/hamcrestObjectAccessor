<?php

declare(strict_types=1);

namespace SebastianKnott\HamcrestObjectAccessor\Test\Unit;

use Hamcrest\Description;
use Hamcrest\Matcher;
use InvalidArgumentException;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use SebastianKnott\DevUtils\Test\Infrastructure\DevToolsTestCase;
use SebastianKnott\HamcrestObjectAccessor\HasProperty;
use SebastianKnott\HamcrestObjectAccessor\Test\Unit\Fixtures\HasPropertyFixture;
use stdClass;

class HasPropertyTest extends DevToolsTestCase
{
    /** @var string */
    private $propertyName;

    /** @var Matcher|LegacyMockInterface|MockInterface $mockedMatcher */
    private $mockedMatcher;

    /** @var HasProperty */
    private $subject;

    /**
     * @test
     */
    public function hasProperty()
    {
        $subject = HasProperty::hasProperty(
            'propertyName',
            'propertyValue'
        );

        self::assertInstanceOf(HasProperty::class, $subject);
    }

    /**
     * @test
     */
    public function describeTo()
    {
        $mockedDescription = mock(Description::class);
        $mockedDescription->shouldReceive('appendText')->once()
            ->with(
                'an object with public property "' . $this->propertyName
                . '" with a value matching '
            );
        $this->mockedMatcher->shouldReceive('describeTo')->once()
            ->with($mockedDescription);

        $this->subject->describeTo($mockedDescription);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @return array<mixed>
     */
    public function matchesReturnsExpectedResultDataProvider(): array
    {
        return [
            'object without property' => [new stdClass(), 'bla', 'blub', false],
            'object with property'    => [
                new HasPropertyFixture(),
                'bla',
                'blub',
                true,
            ],
            'object with getter'      => [
                new HasPropertyFixture(),
                'getable',
                'blub',
                true,
            ],
            'object with isser'       => [
                new HasPropertyFixture(),
                'issable',
                true,
                true,
            ],
            'object with hasser'      => [
                new HasPropertyFixture(),
                'hassable',
                true,
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider matchesReturnsExpectedResultDataProvider
     *
     * @param mixed  $item
     * @param string $propertyName
     * @param string $propertyValue
     * @param bool   $expectedResult
     */
    public function matchesSafelyReturnsExpectedResult(
        $item,
        $propertyName,
        $propertyValue,
        $expectedResult
    ) {
        $subject = HasProperty::hasProperty($propertyName, $propertyValue);
        $result  = $subject->matchesSafely($item);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @test
     */
    public function describeMismatchSafelyReturnsExpectedResult()
    {
        $item          = new stdClass();
        $propertyName  = 'bla';
        $propertyValue = 'blub';

        /** @var MockInterface|Description $mockedDescription */
        $mockedDescription = mock(Description::class);
        $mockedDescription->shouldReceive('appendText')->once()
            ->with(
                'neither the property "' . $propertyName . '" nor one of the methods '
                . '"get' . ucfirst($propertyName) . '()", "' . $propertyName . '()", "is' . ucfirst($propertyName)
                . '()", "has' . ucfirst($propertyName) . '()", "__get()", '
                . '"__call()" exist and have public access in class "stdClass" '
            )->andReturnSelf();

        $subject = HasProperty::hasProperty($propertyName, $propertyValue);
        $subject->describeMismatchSafely($item, $mockedDescription);
    }

    /**
     * @test
     */
    public function matchesSafelyParameterNeedsToBeObject()
    {
        $result = $this->subject->matches('asd');
        self::assertFalse($result);
    }

    /**
     * @test
     */
    public function constructorPropertyNameNeedsToBeString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1596896381);

        new HasProperty(new stdClass(), objectValue());
    }

    /**
     * @test
     */
    public function diagnosticDescriptionIsForwarded()
    {
        $forgedObject      = new stdClass();
        $forgedObject->bla = 'wubwub';

        $mockedMatcher = mock(Matcher::class);
        $mockedMatcher->shouldReceive('describeMismatch')->once()
            ->with('wubwub', anInstanceOf(Description::class));
        $mockedMatcher->shouldReceive('matches')->once()
            ->with('wubwub')->andReturn(true);

        $subject = new HasProperty('bla', $mockedMatcher);
        $subject->matchesSafely($forgedObject);
    }

    protected function setUp()
    {
        $this->propertyName  = 'MyItem';
        $this->mockedMatcher = mock(Matcher::class);

        $this->subject = new HasProperty($this->propertyName, $this->mockedMatcher);
    }
}
