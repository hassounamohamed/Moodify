<?php

namespace App\Controller;

use App\Entity\PlaylistSong;
use App\Repository\PlaylistSongRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistSongController extends AbstractController
{
    #[Route('/playlist-songs', name: 'app_playlist_song_index')]
    public function index(PlaylistSongRepository $playlistSongRepository): Response
    {
        $playlistSongs = $playlistSongRepository->findAll();

        return $this->render('playlist_song/index.html.twig', [
            'playlistSongs' => $playlistSongs,
        ]);
    }
}