<?php

namespace App\Controller;

use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    #[Route('/songs', name: 'song_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $songs = $em->getRepository(Song::class)->findAll();

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/songs/add', name: 'song_add', methods: ['POST', 'GET'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $artiste = $request->request->get('artiste');
            $lienYoutube = $request->request->get('lienYoutube');
            $humeurPrincipale = $request->request->get('humeurPrincipale');

            $song = new Song();
            $song->setTitre($titre)
                 ->setArtiste($artiste)
                 ->setLienYoutube($lienYoutube)
                 ->setHumeurPrincipale($humeurPrincipale);

            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/add.html.twig'); // si besoin d'une page séparée
    }
}
