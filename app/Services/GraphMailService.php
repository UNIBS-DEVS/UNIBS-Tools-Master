<?php

namespace App\Services;

use GuzzleHttp\Client;

class GraphMailService
{
    protected $clientId;
    protected $tenantId;
    protected $clientSecret;
    protected $fromEmail;

    public function __construct($config)
    {
        $this->clientId = $config->graph_client_id;
        $this->tenantId = $config->graph_tenant_id;
        $this->clientSecret = $config->graph_client_secret_value;
        $this->fromEmail = $config->support_user;
    }

    private function getAccessToken()
    {
        $client = new Client();

        $response = $client->post(
            "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);

        return $body['access_token'];
    }

    public function sendMail(
        $to,
        $subject,
        $html,
        $cc = [],
        $bcc = [],
        $attachments = []
    ) {

        $token = $this->getAccessToken();

        $client = new Client();


        $client->post(
            "https://graph.microsoft.com/v1.0/users/{$this->fromEmail}/sendMail",
            [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],

                'json' => [

                    'message' => [

                        'subject' => $subject,

                        'body' => [
                            'contentType' => 'HTML',
                            'content' => $html,
                        ],

                        'toRecipients' => collect((array)$to)
                            ->map(fn($email) => [
                                'emailAddress' => [
                                    'address' => $email
                                ]
                            ])
                            ->values(),

                        'ccRecipients' => collect((array)$cc)
                            ->map(fn($email) => [
                                'emailAddress' => [
                                    'address' => $email
                                ]
                            ])
                            ->values(),

                        'bccRecipients' => collect((array)$bcc)
                            ->map(fn($email) => [
                                'emailAddress' => [
                                    'address' => $email
                                ]
                            ])
                            ->values(),
                    ],

                    'saveToSentItems' => true,
                ],
            ]
        );

        return true;
    }
}
