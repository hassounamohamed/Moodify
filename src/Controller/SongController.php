<?php

// src/Controller/SongController.php
namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/songs')]
class SongController extends AbstractController
{
    #[Route('/', name: 'song_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $songs = $em->getRepository(Song::class)->findAll();

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/new', name: 'song_add', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($song);
            $em->flush();

            $this->addFlash('success', 'La chanson a été ajoutée avec succès!');
            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/new.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'song_show', methods: ['GET'])]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/{id}/edit', name: 'song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La chanson a été modifiée avec succès!');
            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/edit.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'song_delete', methods: ['POST'])]
    public function delete(Request $request, Song $song, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $em->remove($song);
            $em->flush();
            $this->addFlash('success', 'La chanson a été supprimée avec succès!');
        }

        return $this->redirectToRoute('song_index');
    }
}