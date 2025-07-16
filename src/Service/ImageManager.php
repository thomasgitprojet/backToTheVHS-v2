<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageManager
{
    private string $uploadDir;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $params)
    {
        $this->uploadDir = $params->get('kernel.project_dir') . '/public/uploads/images/';
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