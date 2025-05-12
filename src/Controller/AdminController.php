<?php

namespace App\Controller;

use App\Entity\Song;
use App\Entity\User;
use App\Entity\Playlist;
use App\Entity\PlaylistSong; 
use App\Form\SongType;
use App\Form\UserType;
use App\Form\PlaylistType;

use App\Repository\SongRepository;
use App\Repository\UserRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/dashboard.html.twig');
    }
    #[Route('/songs/{id}', name: 'admin_song_show')]
public function showSong(Song $song): Response
{
    return $this->render('admin/song/show.html.twig', [
        'song' => $song
    ]);
}

    // Gestion des chansons
    #[Route('/songs', name: 'admin_song_index')]
    public function songIndex(SongRepository $repository): Response
    {
        $songs = $repository->findAll();
        return $this->render('admin/song/index.html.twig', ['songs' => $songs]);
    }

    #[Route('/songs/new', name: 'admin_song_new')]
    public function newSong(Request $request, EntityManagerInterface $em): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($song);
            $em->flush();
            $this->addFlash('success', 'Chanson créée avec succès');
            return $this->redirectToRoute('admin_song_index');
        }

        return $this->render('admin/song/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/songs/{id}/edit', name: 'admin_song_edit')]
    public function editSong(Song $song, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SongType::class, $song);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Chanson mise à jour');
            return $this->redirectToRoute('admin_song_index');
        }

        return $this->render('admin/song/edit.html.twig', [
            'form' => $form->createView(),
            'song' => $song
        ]);
    }

    #[Route('/songs/{id}/delete', name: 'admin_song_delete', methods: ['POST'])]
public function deleteSong(Song $song, Request $request, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
        // First remove all playlist associations
        foreach ($song->getPlaylistSongs() as $playlistSong) {
            $em->remove($playlistSong);
        }
        
        // Then remove the song itself
        $em->remove($song);
        $em->flush();
        $this->addFlash('success', 'Chanson supprimée');
    }
    return $this->redirectToRoute('admin_song_index');
}

    // Gestion des utilisateurs
    #[Route('/users', name: 'admin_user_index')]
    public function userIndex(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }

    #[Route('/users/{id}/edit', name: 'admin_user_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur mis à jour');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    #[Route('/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
public function deleteUser(User $user, Request $request, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Utilisateur supprimé');
    }
    return $this->redirectToRoute('admin_user_index');
}
#[Route('/playlist-song/{id}', name: 'app_playlist_song_delete', methods: ['POST'])]
public function deletePlaylistSong(PlaylistSong $playlistSong, Request $request, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$playlistSong->getId(), $request->request->get('_token'))) {
        $em->remove($playlistSong);
        $em->flush();
        $this->addFlash('success', 'Association supprimée avec succès');
    }
    return $this->redirectToRoute('app_playlist_song_index');
}
    // Gestion des playlists
    #[Route('/playlists', name: 'admin_playlist_index')]
    public function playlistIndex(PlaylistRepository $repository): Response
    {
        $playlists = $repository->findAll();
        return $this->render('admin/playlist/index.html.twig', ['playlists' => $playlists]);
    }

    #[Route('/playlists/new', name: 'admin_playlist_new')]
    public function newPlaylist(Request $request, EntityManagerInterface $em): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $playlist->setUser($this->getUser());
            $em->persist($playlist);
            $em->flush();
            $this->addFlash('success', 'Playlist créée');
            return $this->redirectToRoute('admin_playlist_index');
        }

        return $this->render('admin/playlist/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/playlists/{id}', name: 'admin_playlist_show')]
    public function showPlaylist(Playlist $playlist): Response
    {
        return $this->render('admin/playlist/show.html.twig', [
            'playlist' => $playlist
        ]);
    }

    #[Route('/playlists/{id}/edit', name: 'admin_playlist_edit')]
    public function editPlaylist(Playlist $playlist, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Playlist mise à jour');
            return $this->redirectToRoute('admin_playlist_index');
        }

        return $this->render('admin/playlist/edit.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist
        ]);
    }

    #[Route('/playlists/{id}/delete', name: 'admin_playlist_delete', methods: ['POST'])]
    public function deletePlaylist(Playlist $playlist, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            $em->remove($playlist);
            $em->flush();
            $this->addFlash('success', 'Playlist supprimée');
        }
        return $this->redirectToRoute('admin_playlist_index');
    }

    #[Route('/playlists/{id}/add-songs', name: 'admin_playlist_add_songs')]
public function addSongsToPlaylist(Playlist $playlist, Request $request, EntityManagerInterface $em, SongRepository $songRepository): Response
{
    $songs = $songRepository->findAll();
    
    if ($request->isMethod('POST')) {
        $selectedSongsIds = $request->request->all()['songs'] ?? [];
        
        foreach ($selectedSongsIds as $songId) {
            $song = $songRepository->find($songId);
            if ($song && !$playlist->containsSong($song)) {
                $playlistSong = new PlaylistSong();
                $playlistSong->setSong($song);
                $playlistSong->setPlaylist($playlist);
                $em->persist($playlistSong);
                $playlist->addPlaylistSong($playlistSong);
            }
        }
        
        $em->flush();
        $this->addFlash('success', 'Chansons ajoutées à la playlist avec succès');
        return $this->redirectToRoute('admin_playlist_show', ['id' => $playlist->getId()]);
    }
    
    return $this->render('admin/playlist/add_songs.html.twig', [
        'playlist' => $playlist,
        'songs' => $songs
    ]);
}
#[Route('/playlists/{id}/remove-song/{songId}', name: 'admin_playlist_remove_song', methods: ['POST'])]
public function removeSongFromPlaylist(Playlist $playlist, int $songId, Request $request, EntityManagerInterface $em, SongRepository $songRepository): Response
{
    if ($this->isCsrfTokenValid('remove_song'.$playlist->getId().$songId, $request->request->get('_token'))) {
        $song = $songRepository->find($songId);
        if ($song) {
            $playlist->removeSong($song);
            $em->flush();
            $this->addFlash('success', 'Chanson retirée de la playlist');
        }
    }
    return $this->redirectToRoute('admin_playlist_show', ['id' => $playlist->getId()]);
}
}