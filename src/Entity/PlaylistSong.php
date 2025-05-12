<?php

namespace App\Entity;

use App\Repository\PlaylistSongRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistSongRepository::class)]
class PlaylistSong
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'playlistSongs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Playlist $playlist = null;

    #[ORM\ManyToOne(inversedBy: 'playlistSongs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Song $song = null;
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;
        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): static
    {
        $this->song = $song;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('PlaylistSong #%d (Playlist: %s, Song: %s)', 
            $this->id ?? 0, 
            $this->playlist?->getNom() ?? 'null', 
            $this->song?->getTitre() ?? 'null'
        );
    }
}