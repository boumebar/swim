<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PoolTest extends ApiTestCase
{

    /****************************************************************************************************
     * 
     *              INITIALISATION DES TOKENS EN USER ET ADMIN
     * 
     *******************************************************************************************************/

    private $userToken;
    private $adminToken;

    //Initialisation et generation de token en user et en admin 
    protected function setUp(): void
    {
        parent::setUp();

        $client = static::createClient();

        // Créer un utilisateur normal
        $userData = [
            'email' => 'user1@user.com',
            'password' => 'password',
        ];
        $client->request('POST', '/api/login_check', [
            'json' => $userData,
        ]);
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->userToken = $data['token']; // Stocker le token de l'utilisateur

        // Créer un administrateur
        $adminData = [
            'email' => 'admin@admin.com',
            'password' => 'password',
        ];
        $client->request('POST', '/api/login_check', [
            'json' => $adminData,
        ]);
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->adminToken = $data['token']; // Stocker le token de l'administrateur
    }


    /*********************************************************************************************************
     *          
     *                              METHODES GET
     * 
     ********************************************************************************************************/


    /*********************** PAS AUTHENTIFIE************************ */


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


    /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de l'accès à la route en GET /api/pools en tant qu'utilisateur authentifié
    public function testGetPoolsAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à la route en GET /api/pools/{id} en tant qu'utilisateur authentifié
    public function testGetPoolAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools/1', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à une piscine inexistante
    public function testGetNonExistentPool(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools/1111', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    /*********************** AUTHENTIFIE EN ADMIN ************************ */


    // Test de l'accès à la route en GET /api/pools en tant qu'administrateur authentifié
    public function testGetPoolsAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à la route en GET /api/pools/{id} en tant qu'administrateur authentifié
    public function testGetPoolAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools/1', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à une piscine inexistante en tant qu'administrateur
    public function testGetNonExistentPoolAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/pools/1000', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    /*********************************************************************************************************
     *          
     *                              METHODES POST
     * 
     ********************************************************************************************************/


    /*********************** PAS AUTHENTIFIE************************ */


    // Test de l'acces a la route /api/pools en POST pas authentifie
    public function testPostPoolUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'POST',
            '/api/pools',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }

    /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de la création d'une piscine avec des données valides mais pas les droits 
    public function testCreatePoolWithValidDataForbidden(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 1,
            'name' => 'Piscine test',
            'description' => 'Description de la piscine',
            'pricePerDay' => 50.0,
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 403 Forbidden
        $this->assertResponseStatusCodeSame(403);
    }

    // Test de la création d'une piscine avec des données invalides et pas les droits 
    public function testCreatePoolWithInvalidDataForbidden(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 1,
            'name' => 'Piscine test',
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 403 Forbidden
        $this->assertResponseStatusCodeSame(403);
    }


    /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de la création d'une piscine avec des données valides 
    public function testCreatePoolWithValidData(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/1',
            'name' => 'Piscine test',
            'description' => 'Description de la piscine',
            'pricePerDay' => '5000',
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 201 Created
        $this->assertResponseStatusCodeSame(201);
    }

    // Test de la création d'une piscine avec des données invalides avec les droits 
    public function testCreatePoolWithInvalidData(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/1',
            'name' => 'Piscine test',
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }


    /*********************************************************************************************************
     *          
     *                              METHODES PATCH
     * 
     ********************************************************************************************************/

    /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/pools/{id} en PATCH pas authentifie
    public function testPatchPoolUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/1',
            [
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
                'json' => [
                    'name' => 'Piscine test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    /*********************** AUTHENTIFIE EN USER ************************ */

    // Test de l'acces a la route /api/pools/{id} en PATCH authentifie user
    public function testPatchPoolAuthenticatedUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/1',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ],
                'json' => [
                    'name' => 'Piscine test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(403);
    }


    /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/pools/{id} en PATCH authentifie admin
    public function testPatchPoolAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/1',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'name' => 'Piscine test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/pools/{id} en PATCH authentifie admin mais avec id inexistant
    public function testPatchPoolAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'name' => 'Piscine test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }



    /*********************************************************************************************************
     *          
     *                              METHODES PUT
     * 
     ********************************************************************************************************/

    /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/pools/{id} en PUT pas authentifie
    public function testPutPoolUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/1',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
                'json' => [
                    'name' => 'Piscine test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    /*********************** AUTHENTIFIE EN USER ************************ */

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie user
    public function testPutPoolAuthenticatedUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ],
                'json' => [
                    'owner' => 1,
                    'name' => 'Piscine test',
                    'description' => 'Description de la piscine',
                    'pricePerDay' => 50.0,
                    'location' => 'Paris'
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(403);
    }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie admin
    public function testPutPoolAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'owner' => 'api/users/2',
                    'name' => 'Piscine test1',
                    'description' => 'Description de la piscine',
                    'pricePerDay' => '5000',
                    'location' => 'Paris'
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutPoolAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'owner' => 'api/users/2',
                    'name' => 'Piscine test1',
                    'description' => 'Description de la piscine',
                    'pricePerDay' => '5000',
                    'location' => 'Paris'
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutPoolAuthorizedWithInvalidData(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'owner' => 'api/users/2'
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(422);
    }

    /*********************************************************************************************************
     *          
     *                              METHODES DELETE
     * 
     ********************************************************************************************************/

    /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/pools/{id} en DELETE pas authentifie
    public function testDeletePoolUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('DELETE', '/api/pools/1');
        $this->assertResponseStatusCodeSame(401);
    }
}
