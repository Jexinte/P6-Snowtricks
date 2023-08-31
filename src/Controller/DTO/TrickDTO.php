<?php

namespace App\Controller\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class TrickDTO
{
    public string $name;
    public string $description;
    public string $trickGroup;
    public ?array $illustrations;
    public ?array $videos;

    public function getName():string
    {
        return $this->name;
    }
    public function setName(string $name):void
    {
        $this->name = $name;
    }
    public function getTrickGroup():string
    {
        return $this->trickGroup;
    }
    public function setTrickGroup(string $trickGroup):void
    {
        $this->trickGroup = $trickGroup;
    }

    public function getIllustrations(): array
    {
        return $this->illustrations;
    }
    public function setIllustrations(array $illustrations):void
    {
        $this->illustrations = $illustrations;
    }
    public function getVideos(): array
    {
        return $this->videos;
    }
    public function setVideos(array $videos):void
    {
        $this->videos = $videos;
    }

}
