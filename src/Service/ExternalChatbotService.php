<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ExternalChatbotService {
    private $client;
    private $apiKey;
    private $logger;
    
    public function __construct(
        HttpClientInterface $client,
        string $apiKey,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }
    
    public function askAI(string $prompt): string {
        try {
            // Logging de la demande
            $this->logger->info('Demande entrante au chatbot', ['prompt' => $prompt]);
            
            // Préparation des données pour OpenRouter
            $requestData = [
                'model' => 'anthropic/claude-3-haiku',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert musical. Réponds avec 2 suggestions musicales.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 100
            ];
            
            // Logs pour le débogage
            $this->logger->debug('Données de requête vers OpenRouter', ['data' => $requestData]);
            
            // Appel à l'API OpenRouter
            $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'HTTP-Referer' => 'https://moodify.com',
                    'X-Title' => 'Moodify Bot',
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
                'timeout' => 60
            ]);
            
            // Récupération de la réponse
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
            
            // Logs pour le débogage
            $this->logger->debug('Réponse brute de OpenRouter', [
                'statusCode' => $statusCode,
                'content' => $content
            ]);
            
            // Vérification du status code
            if ($statusCode !== 200) {
                throw new \Exception("Erreur API OpenRouter: Code $statusCode");
            }
            
            // Décodage de la réponse JSON
            $data = json_decode($content, true);
            
            // Vérification de la structure de la réponse
            if (!is_array($data) || !isset($data['choices'][0]['message']['content'])) {
                $this->logger->error('Structure de réponse OpenRouter invalide', ['data' => $data]);
                throw new \Exception('Structure de réponse invalide de OpenRouter');
            }
            
            // Extraction de la réponse
            $aiResponse = $data['choices'][0]['message']['content'];
            $this->logger->info('Réponse du chatbot', ['response' => $aiResponse]);
            
            return $aiResponse;
            
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de l\'appel à OpenRouter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}