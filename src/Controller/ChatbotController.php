<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Form\ChatbotType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chatbot', name: 'chatbot')]
final class ChatbotController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $conversation = new Conversation();
        $form = $this->createForm(ChatbotType::class, $conversation);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $conversation->setUser($this->getUser());
            $conversation->setMood($this->analyzeMood($conversation->getMessage()));
            
            // Génère la réponse du chatbot
            $response = $this->generateChatbotResponse($conversation->getMessage());
            $conversation->setResponse($response);
            
            $em->persist($conversation);
            $em->flush();
            
            return $this->redirectToRoute('app_chatbot_index');
        }
        
        return $this->render('chatbot/index.html.twig', [
            'form' => $form->createView(),
            'conversations' => $em->getRepository(Conversation::class)
                ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC'])
        ]);
    }

    private function analyzeMood(string $message): string
    {
        $positiveWords = ['joyeux', 'heureux', 'content', 'super'];
        $negativeWords = ['triste', 'malheureux', 'déprimé', 'fatigué'];
        
        foreach ($positiveWords as $word) {
            if (stripos($message, $word) !== false) {
                return 'positive';
            }
        }
        foreach ($negativeWords as $word) {
            if (stripos($message, $word) !== false) {
                return 'negative';
            }
        }
        return 'neutral';
    }

    private function generateChatbotResponse(string $message): string
    {
        $mood = $this->analyzeMood($message);
        $responses = [
            'positive' => [
                "Je vois que vous êtes de bonne humeur ! Quelle musique voulez-vous écouter aujourd'hui ?",
                "Votre énergie positive est contagieuse ! Essayez cette playlist spéciale..."
            ],
            'negative' => [
                "Je sens que vous pourriez avoir besoin d'une musique réconfortante...",
                "Une mauvaise journée ? La musique peut aider à améliorer les choses."
            ],
            'neutral' => [
                "Dites-moi plus sur ce que vous ressentez pour que je puisse trouver la musique parfaite.",
                "Quel genre de musique recherchez-vous en ce moment ?"
            ]
        ];

        return $responses[$mood][array_rand($responses[$mood])];
    }
}
