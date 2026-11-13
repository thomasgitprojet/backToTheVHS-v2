<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager
{
    public function __construct(
        #[Autowire('%uploads_directory%')]
        private string $uploadDir,
        private Filesystem $filesystem,
    ) {
    }

    /**
     * Enregistre l'image dans le dossier d'upload et renvoie le nom du fichier créé.
     */
    public function uploadImage(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($this->uploadDir, $filename);

        return $filename;
    }

    /**
     * Supprime une image du dossier d'upload, si elle existe.
     */
    public function deleteImage(string $filename): void
    {
        $filePath = $this->uploadDir . '/' . $filename;

        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }
    }
}