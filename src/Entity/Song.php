<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $artiste = null;

    #[ORM\Column(length: 255)]
    private ?string $lienYoutube = null;

    #[ORM\Column(length: 255)]
    private ?string $humeurPrincipale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getArtiste(): ?string
    {
        return $this->artiste;
    }

    public function setArtiste(string $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }

    public function getLienYoutube(): ?string
    {
        return $this->lienYoutube;
    }

    public function setLienYoutube(string $lienYoutube): static
    {
        $this->lienYoutube = $lienYoutube;

        return $this;
    }

    public function getHumeurPrincipale(): ?string
    {
        return $this->humeurPrincipale;
    }

    public function setHumeurPrincipale(string $humeurPrincipale): static
    {
        $this->humeurPrincipale = $humeurPrincipale;

        return $this;
    }
}
