<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $humeur = null;

    #[ORM\OneToMany(mappedBy: 'playlist', targetEntity: PlaylistSong::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $playlistSongs;

    #[ORM\ManyToOne(inversedBy: 'playlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->playlistSongs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getHumeur(): ?string
    {
        return $this->humeur;
    }

    public function setHumeur(string $humeur): static
    {
        $this->humeur = $humeur;
        return $this;
    }

    public function getPlaylistSongs(): Collection
    {
        return $this->playlistSongs;
    }

    public function addPlaylistSong(PlaylistSong $playlistSong): static
    {
        if (!$this->playlistSongs->contains($playlistSong)) {
            $this->playlistSongs->add($playlistSong);
            $playlistSong->setPlaylist($this);
        }
        return $this;
    }

    public function removePlaylistSong(PlaylistSong $playlistSong): static
    {
        if ($this->playlistSongs->removeElement($playlistSong)) {
            if ($playlistSong->getPlaylist() === $this) {
                $playlistSong->setPlaylist(null);
            }
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSongs(): Collection
    {
        $songs = new ArrayCollection();
        foreach ($this->playlistSongs as $playlistSong) {
            if ($song = $playlistSong->getSong()) {
                $songs->add($song);
            }
        }
        return $songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->containsSong($song)) {
            $playlistSong = new PlaylistSong();
            $playlistSong->setSong($song);
            $playlistSong->setPlaylist($this);
            $this->addPlaylistSong($playlistSong);
        }
        return $this;
    }

    public function removeSong(Song $song): static
    {
        foreach ($this->playlistSongs as $playlistSong) {
            if ($playlistSong->getSong() === $song) {
                $this->removePlaylistSong($playlistSong);
                break;
            }
        }
        return $this;
    }

    public function containsSong(Song $song): bool
    {
        foreach ($this->playlistSongs as $playlistSong) {
            if ($playlistSong->getSong() === $song) {
                return true;
            }
        }
        return false;
    }
}