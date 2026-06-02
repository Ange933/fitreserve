<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeanceControllerTest extends WebTestCase
{
    public function testListSeancesIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/seances');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testCreateSeanceRequiresAdmin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/seances', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'titre' => 'Test Séance',
            'dateHeure' => (new \DateTime('+1 week'))->format('c'),
            'duree' => 60,
            'placesMax' => 10,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }
}
