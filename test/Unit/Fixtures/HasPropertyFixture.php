<?php

declare(strict_types=1);

namespace SebastianKnott\HamcrestObjectAccessor\Test\Unit\Fixtures;

class HasPropertyFixture
{
    /** @var string */
    public $bla = 'blub';

    /** @var string */
    private $getable = 'blub';

    /** @var bool */
    private $issable = true;

    /** @var bool */
    private $hassable = true;

    public function getGetable()
    {
        return $this->getable;
    }

    public function isIssable()
    {
        return $this->issable;
    }

    public function hasHassable()
    {
        return $this->hassable;
    }
}
