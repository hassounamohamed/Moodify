<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    // Route 1: Liste toutes les playlists
    #[Route('/playlists', name: 'app_playlist_index')]
    public function index(PlaylistRepository $playlistRepository): Response
    {
        $playlists = $playlistRepository->findAll();
        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    // Route 2: Affiche une playlist spécifique
    #[Route('/playlist/{id}', name: 'app_playlist_show')]
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
            // 'songs' => $playlist->getSongs() // À décommenter quand vous aurez la relation
        ]);
    }

    // Route 3: Nouvelle route pour filtrer par humeur
    #[Route('/playlists/humeur/{humeur}', name: 'app_playlist_by_mood')]
    public function byMood(string $humeur, PlaylistRepository $playlistRepository): Response
    {
        $playlists = $playlistRepository->findBy(['humeur' => $humeur]);
        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlists,
            'current_mood' => ucfirst($humeur)
        ]);
    }
}