<?php

namespace App\Domain\Port;

interface UuidGeneratorInterface
{
    public function generateUuid(): string;
}
