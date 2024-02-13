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
    // Le nom du destinataire et le lien du reset password, le chemin et le nom du pdf sont optionnels
    // Ex: $this->emailService->sendEmail(5638161, $client->getEmail(), $client->getName, etc, etc, etc);
    // L'id des templates est retrouvable sur le compte MailJet

    public function sendEmail(int $templateId, string $recipientEmail, string $recipientName='', string $resetPwdLink = '', string $pdfFilePath = '', string $fileName = '', string $paymentLink = '') 
    {
        // Injection des clé API
        $mj = new Client($this->mailjetApiKey, $this->mailjetApiSecret, true, ['version' => 'v3.1']);

        $attachments = $pdfFilePath ? [
            [
                'ContentType' => 'application/pdf',
                'Filename' => $fileName. '.pdf',
                'Base64Content' => base64_encode(file_get_contents($pdfFilePath))
            ]
        ] : null;
         
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
                        'paymentLink' => $paymentLink,
                    ],
                    'Attachments' => $attachments,
                ],
            ],
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);

        return $response->success();
    }
}