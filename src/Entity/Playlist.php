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

    #[ORM\OneToMany(mappedBy: 'playlist', targetEntity: PlaylistSong::class, orphanRemoval: true)]
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
}