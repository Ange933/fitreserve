<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testRegisterWithValidData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'password' => 'Test1234!',
            'nom' => 'Test',
            'prenom' => 'User',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testRegisterWithInvalidEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'not-an-email',
            'password' => 'Test1234!',
            'nom' => 'Test',
            'prenom' => 'User',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRegisterWithWeakPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'valid@example.com',
            'password' => 'weak',
            'nom' => 'Test',
            'prenom' => 'User',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'admin@fitreserve.fr',
            'password' => 'Admin1234!',
        ]));

        $this->assertResponseIsSuccessful();
    }
}
