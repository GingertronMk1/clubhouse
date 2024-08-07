<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

abstract class AbstractId implements \Stringable, \JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return (string) $this;
    }
}
