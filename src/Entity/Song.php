<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'song', targetEntity: PlaylistSong::class)]
    private Collection $playlistSongs;

    public function __construct()
    {
        $this->playlistSongs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, PlaylistSong>
     */
    public function getPlaylistSongs(): Collection
    {
        return $this->playlistSongs;
    }

    public function addPlaylistSong(PlaylistSong $playlistSong): static
    {
        if (!$this->playlistSongs->contains($playlistSong)) {
            $this->playlistSongs->add($playlistSong);
            $playlistSong->setSong($this);
        }
        return $this;
    }

    public function removePlaylistSong(PlaylistSong $playlistSong): static
    {
        if ($this->playlistSongs->removeElement($playlistSong)) {
            if ($playlistSong->getSong() === $this) {
                $playlistSong->setSong(null);
            }
        }
        return $this;
    }
}