<?php

namespace App\core\Upload;

interface UploadedFileInterface
{
    public function move(string $path, string $fileName = null): string|false;

    public function getExtension(): string;

    public function hasError(): bool;
}
