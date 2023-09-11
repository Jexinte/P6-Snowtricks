<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(
    fields: 'name',
    message: "Le nom de votre trick existe déjà, merci d'en choisir un autre !",
    groups: ["name_exception"]
)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'name_exception',
        ]
    )]

    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
        match: true,
        groups: ['name_exception']
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'description_exception',
        ]
    )]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, votre description doit commencer par une lettre majuscule',
        match: true,
        groups: ['description_exception']
    )]
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'group_exception',
        ]
    )]
    #[ORM\Column(length: 255)]
    private ?string $trickGroup = null;


    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'name_update_exception',
        ]
    )]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
        match: true,
        groups: ['name_update_exception']
    )]
    private string $nameUpdated;
//    #[OneToMany(mappedBy: "trick", targetEntity: (Media::class))]
//    private $medias;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTrickGroup(): ?string
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(?string $trickGroup): static
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setDate(?\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getNameUpdated(): string
    {
        return $this->nameUpdated;
    }

    /**
     * @param string $nameUpdated
     */
    public function setNameUpdated(string $nameUpdated): void
    {
        $this->nameUpdated = $nameUpdated;
    }


}
