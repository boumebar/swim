<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PoolTest extends ApiTestCase
{

    /******************************************************
     *          PAS authentifie : GET / POST / PUT / PATCH /  *         DELETE
     * 
     ******************************************************/

    // Test de l'accès à la route /api/pools pas authentifie
    public function testGetPoolsUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('GET', '/api/pools');
        $this->assertResponseStatusCodeSame(401);
    }

    // Test de l'accès à la route /api/pools/{id} pas authentifie
    public function testGetPoolUnauthorized(): void
    {
        // Test d’accès à la route sans authentification
        static::createClient()->request('GET', '/api/pools/1');
        $this->assertResponseStatusCodeSame(401);
    }

    // // Test de l'acces a la route /api/pools en POST pas authentifie
    // public function testPostPoolUnauthorized(): void
    // {
    //     // Test d'accès à la route sans authentification
    //     static::createClient()->request('POST', '/api/pools');
    //     $this->assertResponseStatusCodeSame(401);
    // }

    // // Test de l'acces a la route /api/pools/{id} en PUT pas authentifie
    // public function testPutPoolUnauthorized(): void
    // {
    //     // Test d'accès à la route sans authentification
    //     static::createClient()->request('PUT', '/api/pools/1');
    //     $this->assertResponseStatusCodeSame(401);
    // }

    // // Test de l'acces a la route /api/pools/{id} en PATCH pas authentifie
    // public function testPatchPoolUnauthorized(): void
    // {
    //     // Test d'accès à la route sans authentification
    //     static::createClient()->request('PATCH', '/api/pools/1');
    //     $this->assertResponseStatusCodeSame(401);
    // }

    // Test de l'acces a la route /api/pools/{id} en DELETE pas authentifie
    public function testDeletePoolUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('DELETE', '/api/pools/1');
        $this->assertResponseStatusCodeSame(401);
    }

    // /******************************************************
    //  *          Authentifie en USER : GET / POST / PUT / *          PATCH  / DELETE
    //  * 
    //  * *****************************************************/



    // // Test de l'acces a la route /api/pools en GET authentifie
    // public function testGetPoolsAuthorized(): void
    // {
    //     // Test d'accès avec un token JWT valide
    //     $response = static::createClient()->request('POST', '/api/login_check', [
    //         'json' => ['email' => 'user@example.com', 'password' => 'password'],
    //     ]);

    //     $this->assertResponseIsSuccessful();
    //     $token = $response->toArray()['token'];

    //     static::createClient()->request('GET', '/api/pools', [], ['auth_bearer' => $token]);
    //     $this->assertResponseIsSuccessful();
    // }
}
