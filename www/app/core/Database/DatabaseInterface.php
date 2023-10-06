<?php

namespace App\core\Database;

interface DatabaseInterface
{
    public function create(object $entity, string $table): int|string;

    public function update(object $obj, string $table): int|string;
}