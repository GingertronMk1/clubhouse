<?php

declare(strict_types=1);

namespace App\Application\Sport\Command;

class CreateSportCommand
{
    public function __construct(
        public string $name = '',
        public ?string $description = null,
    ) {
    }
}
