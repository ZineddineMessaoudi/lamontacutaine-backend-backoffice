<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload($fileType, UploadedFile $file, $isPortrait = false): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        if($isPortrait)
        {
            $fileName = 'mobile_' . $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        } else {
            $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        }

        try {
            $file->move($this->getTargetDirectory($fileType), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory($fileType): string
    {
        if ($fileType == 'image')
        {
            return $this->targetDirectory[0];
        }
        else if ($fileType == 'flyer')
        {
            return $this->targetDirectory[1];
        }
        else if ($fileType == 'document_adherent')
        {
            return $this->targetDirectory[3];
        }
        else
        {
            return $this->targetDirectory[2];
        }
        
    }
}