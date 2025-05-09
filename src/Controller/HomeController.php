<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SongRepository;
use App\Repository\PlaylistRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SongRepository $songRepository, PlaylistRepository $playlistRepository): Response
    {
        // If you want to force login for all home page access, uncomment:
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // If you want to show different content based on authentication:
        if (!$this->getUser()) {
            // Option 1: Redirect to login
            // return $this->redirectToRoute('app_login');
            
            // Option 2: Show public content (current implementation)
        }
        // Récupérer les statistiques pour la page d'accueil
        $stats = [
            'total_songs' => $songRepository->count([]),
            'total_playlists' => $playlistRepository->count([]),
            'popular_moods' => $playlistRepository->findMostPopularMoods(3),
        ];

        // Témoignages fictifs (à remplacer par une entrée en base si nécessaire)
        $testimonials = [
            [
                'author' => 'Jean D.',
                'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'content' => 'Moodify a révolutionné ma façon d\'écouter de la musique. Les recommandations sont toujours parfaites!',
                'rating' => 5
            ],
            [
                'author' => 'Sophie M.',
                'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'content' => 'Je ne savais pas que la musique pouvait autant influencer mon humeur avant d\'utiliser Moodify.',
                'rating' => 4
            ],
            [
                'author' => 'Mohamed H.',
                'avatar' => 'https://randomuser.me/api/portraits/men/75.jpg',
                'content' => 'L\'analyse d\'humeur par IA est bluffante. Exactement ce qu\'il me fallait après une longue journée.',
                'rating' => 5
            ]
        ];

        return $this->render('home/index.html.twig', [
            'stats' => $stats,
            'testimonials' => $testimonials,
            'featured_playlists' => $playlistRepository->findFeaturedPlaylists()
        ]);
    }
}