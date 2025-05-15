<?php

namespace App\Controller;

use App\Service\ExternalChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController {
    #[Route('/chatbot', name: 'app_chatbot')]
    public function chatbotInterface(): Response
    {
        return $this->render('chatbot/index.html.twig');
    }
    
    #[Route('/api/chatbot', name: 'api_chatbot', methods: ['POST'])]
    public function handleChatMessage(Request $request, ExternalChatbotService $chatbot): JsonResponse
    {
        // Récupération du contenu JSON
        $data = json_decode($request->getContent(), true);
        
        // Vérifier si le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'response' => "Format de requête invalide",
                'error' => "Invalid JSON"
            ], 400);
        }
        
        // Récupération du message
        $message = trim($data['message'] ?? '');
        
        // Vérification du CSRF token si nécessaire
        /*
        if (!$this->isCsrfTokenValid('chatbot', $data['_csrf_token'] ?? '')) {
            return $this->json([
                'response' => "Erreur de sécurité",
                'error' => "Invalid CSRF token"
            ], 403);
        }
        */
        
        try {
            $response = $chatbot->askAI($message);
            return $this->json(['response' => $response]);
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            $this->container->get('logger')->error('Chatbot API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json([
                'response' => "Désolé, je ne peux pas répondre pour le moment. Réessayez plus tard !",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}