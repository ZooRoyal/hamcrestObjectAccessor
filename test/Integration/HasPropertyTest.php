<?php

declare(strict_types=1);

namespace SebastianKnott\HamcrestObjectAccessor\Test\Integration;

use Hamcrest\AssertionError;
use Hamcrest\MatcherAssert;
use PHPUnit\Framework\TestCase;
use SebastianKnott\HamcrestObjectAccessor\HasProperty;
use SebastianKnott\HamcrestObjectAccessor\Test\Unit\Fixtures\HasPropertyFixture;

class HasPropertyTest extends TestCase
{
    /**
     * @test
     */
    public function hasProperty()
    {
        $object = new HasPropertyFixture();
        MatcherAssert::assertThat($object, hasProperty('bla', stringValue()));
    }

    /**
     * @test
     */
    public function hasPropertyByNamespace()
    {
        $object = new HasPropertyFixture();
        MatcherAssert::assertThat($object, HasProperty::hasProperty('bla', stringValue()));
    }

    /**
     * @test
     */
    public function hasPropertyThrowsExpectedException()
    {
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('neither the property "blarg" nor one of the methods');
        $object = new HasPropertyFixture();
        MatcherAssert::assertThat($object, hasProperty('blarg', intValue()));
    }

    protected function setUp()
    {
        require_once __DIR__ . '/../../src/functions.php';
    }
}
