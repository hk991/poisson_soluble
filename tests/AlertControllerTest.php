<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlertControllerTest extends WebTestCase
{
    public function testSendAlertsWithPostMethod(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $apiKey = $container->getParameter('api_key');

        $client->request(
            'POST',
            '/alerter',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_API_KEY' => $apiKey,
            ],
            json_encode(['insee' => '75056'])
        );

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testSendAlertsMissingInsee(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $apiKey = $container->getParameter('api_key');

        $client->request(
            'POST',
            '/alerter',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_API_KEY' => $apiKey,
            ],
            json_encode([])
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Missing or invalid JSON payload', $client->getResponse()->getContent());
    }

    public function testSendAlertsInvalidContentType(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $apiKey = $container->getParameter('api_key');

        $client->request(
            'POST',
            '/alerter',
            [],
            [],
            [
                'CONTENT_TYPE' => 'text/plain',
                'HTTP_X_API_KEY' => $apiKey,
            ],
            '{"insee": "75056"}'
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Incorrect Content-Type', $client->getResponse()->getContent());
    }
}
