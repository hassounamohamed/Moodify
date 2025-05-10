<?php

namespace App\Controller;

use App\Entity\PlaylistSong;
use App\Form\PlaylistSongType;
use App\Repository\PlaylistSongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/playlist-song')]
class PlaylistSongController extends AbstractController
{
    #[Route('/', name: 'app_playlist_song_index', methods: ['GET'])]
    public function index(PlaylistSongRepository $playlistSongRepository): Response
    {
        return $this->render('playlist_song/index.html.twig', [
            'playlistSongs' => $playlistSongRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_playlist_song_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $playlistSong = new PlaylistSong();
        $form = $this->createForm(PlaylistSongType::class, $playlistSong);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($playlistSong);
            $entityManager->flush();

            $this->addFlash('success', 'Association créée avec succès');
            return $this->redirectToRoute('app_playlist_song_index');
        }

        return $this->render('playlist_song/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_song_show', methods: ['GET'])]
    public function show(PlaylistSong $playlistSong): Response
    {
        return $this->render('playlist_song/show.html.twig', [
            'playlist_song' => $playlistSong,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_playlist_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlaylistSong $playlistSong, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaylistSongType::class, $playlistSong);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Association modifiée avec succès');
            return $this->redirectToRoute('app_playlist_song_index');
        }

        return $this->render('playlist_song/edit.html.twig', [
            'playlist_song' => $playlistSong,
            'form' => $form->createView(),
        ]);
    }

        #[Route('/{id}', name: 'app_playlist_song_delete', methods: ['POST'])]
    public function delete(Request $request, PlaylistSong $playlistSong, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlistSong->getId(), $request->request->get('_token'))) {
            $entityManager->remove($playlistSong);
            $entityManager->flush();
            $this->addFlash('success', 'Association supprimée avec succès');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression');
        }

        return $this->redirectToRoute('app_playlist_song_index');
    }
}