<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{
    private string $uploadDir;
    private Filesystem $filesystem;

    public function __construct(string $projectDir)
    {
        $this->uploadDir = $projectDir . '/public/uploads/images/';
        $this->filesystem = new Filesystem();
    }

    public function deleteImage(string $filename): void
    {
        $filePath = $this->uploadDir . $filename;

        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }
    }
}