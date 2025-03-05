<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private $whatsappFromNumber;
    private $apiUrl;
    private $authToken;
    private $sid;

    public function __construct()
    {
        $this->whatsappFromNumber = env('WHATSAPP_FROM_NUMBER');
        $this->apiUrl = env('WHATSAPP_API_URL');
        $this->authToken = env('WHATSAPP_AUTH_TOKEN');
        $this->sid = env('WHATSAPP_SID');
    }

    public static function sendMessage($to, $message)
    {
        $instance = new self();
        $client = new Client();

        $data = [
            'to' => "whatsapp:$to",
            'from' => "whatsapp:{$instance->whatsappFromNumber}",
            'body' => $message,
        ];

        try {
            $response = $client->post($instance->apiUrl, [
                'form_params' => $data,
                'auth' => [$instance->sid, $instance->authToken],
            ]);

            if ($response->getStatusCode() == 200) {
                Log::info("Message envoyé avec succès à $to");
                return true;
            } else {
                Log::error("Erreur lors de l'envoi du message à $to: " . $response->getBody());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Erreur d'API WhatsApp: " . $e->getMessage());
            return false;
        }
    }
}
