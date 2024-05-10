<?php

namespace App\Infra\Uuid;

use App\Domain\Port\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements UuidGeneratorInterface
{
    public function generateUuid(): string
    {
        return (string) Uuid::uuid4();
    }
}