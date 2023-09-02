<?php

namespace App\Controller\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class TrickDTO
{
    public string $name;
    public int $id;
    public string $description;
    public string $trickGroup;

    /**
     * @var array<string>
     */
    public ?array $illustrations;
    /**
     * @var array<string>
     */
    public ?array $videos;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTrickGroup(): string
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(string $trickGroup): void
    {
        $this->trickGroup = $trickGroup;
    }

    /**
     * @return string[]
     */
    public function getIllustrations(): array
    {
        return $this->illustrations;
    }

    /**
     * @param array<string> $illustrations
     * @return void
     */
    public function setIllustrations(array $illustrations): void
    {
        $this->illustrations = $illustrations;
    }

    /**
     * @return string[]
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * @param array<string> $videos
     * @return void
     */
    public function setVideos(array $videos): void
    {
        $this->videos = $videos;
    }

}
