<?php 

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class EmailService
{
    private $mailjetApiKey;
    private $mailjetApiSecret;

    public function __construct(string $mailjetApiKey, string $mailjetApiSecret)
    {
        $this->mailjetApiKey = $mailjetApiKey;
        $this->mailjetApiSecret = $mailjetApiSecret;
    }


    // Methode sendEmail qui devra etre appeller à chaque fois qu'on voudra faire un envoie de mail dans l'application 
    // Le nom du destinataire et le lien du reset password sont optionnels
    // Ex: $this->emailService->sendEmail(5638161, $client->getEmail(), $client->getName);
    // L'id des templates est retrouvable sur le compte MailJet

    public function sendEmail(int $templateId, string $recipientEmail, string $recipientName='', string $resetPwdLink = '') 
    {
        // Injection des clé API
        $mj = new Client($this->mailjetApiKey, $this->mailjetApiSecret, true, ['version' => 'v3.1']);

        // body du mail
        $body = [
            'Messages' => [
                [
                    'To' => [
                        [
                            'Email' => $recipientEmail,
                            'Name' => $recipientName,
                        ],
                    ],
                    'TemplateID' => $templateId, 
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'resetPwdLink' => $resetPwdLink,
                    ],
                ],
            ],
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        return $response->success();
    }
}