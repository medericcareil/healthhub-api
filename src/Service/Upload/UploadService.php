<?php

namespace App\Service\Upload;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * class UploadService
 * @package App\Service\Upload
 */
class UploadService
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;
    
    /**
     * @var string
     */
    private string $targetDirectory;

    public function __construct(SluggerInterface $slugger, string $targetDirectory)
    {
        $this->slugger = $slugger;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile|null $file
     * @return String|null
     */
    public function processFile(?UploadedFile $file)
    {
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getTargetDirectory(), $newFilename);
            } catch (FileException $e) {
                throw new \Exception($e->getMessage());
            }
            return $newFilename;
        }
    }

    /**
     * @return string 
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
