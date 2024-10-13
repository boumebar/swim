<?php

namespace App\Tests;

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


    // Test de la création d'une piscine avec des données valides en tant qu'utilisateur
    public function testCreatePoolWithValidDataUser(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'Piscine test',
            'description' => 'Description de la piscine',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 201 Created
        $this->assertResponseStatusCodeSame(201);
    }

    // Test de la création d'une piscine avec des données en tant qu'utilisateur
    public function testCreatePoolWithInvalidDataUser(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'Piscine test',
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le nom vide en tant qu'utilisateur
    public function testCreatePoolWithEmptyNamePool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => '',
            'description' => 'Description de la piscine',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le nom trop court en tant qu'utilisateur
    public function testCreatePoolWithNotValidLengthPoolName(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'zz',
            'description' => 'Description de la piscine',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le nom trop long en tant qu'utilisateur
    public function testCreatePoolWithNotValidLengthUserTooLong(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'description' => 'Description de la piscine',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec la description vide en tant qu'utilisateur
    public function testCreatePoolWithEmptyDescriptionPool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => '',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec la description trop courte en tant qu'utilisateur
    public function testCreatePoolWithDescriptionPoolTooShort(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'zz',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le prix negatif en tant qu'utilisateur
    public function testCreatePoolWithNegativePricePool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "-6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le prix nul en tant qu'utilisateur
    public function testCreatePoolWithNullPricePool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le prix supérieur a 1000000 en tant qu'utilisateur
    public function testCreatePoolWithPriceMoreThanPool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "20000000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec la location nul en tant qu'utilisateur
    public function testCreatePoolWithNullLocationPool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "6000",
            'location' => ''
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec la location trop courte en tant qu'utilisateur
    public function testCreatePoolWithLocationPoolTooShort(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "6000",
            'location' => 'Pa'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec la location trop longue en tant qu'utilisateur
    public function testCreatePoolWithLocationPoolTooLong(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => 'api/users/4',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "6000",
            'location' => 'PapppppppppppppppppppppppppppppppppppppppppppppppppppppppppPapppppppppppppppppppppppppppppppppppppppppppppppppppppppppPapppppppppppppppppppppppppppppppppppppppppppppppppppppppppPapppppppppppppppppppppppppppppppppppppppppppppppppppppppppPapppppppppppppppppppppppppppppppppppppppppppppppppppppppppPappppppppppppppppppppppppppppppppppppppppppppppppppppppppp'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // test de la création d'une piscine avec le owner qui est vide en tant qu'utilisateur
    public function testCreatePoolWithEmptyOwnerPool(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'owner' => '',
            'name' => 'boum',
            'description' => 'Nouvelle description',
            'pricePerDay' => "6000",
            'location' => 'Paris'
        ];

        // Effectuer une requête POST pour créer une piscine
        static::createClient()->request('POST', '/api/pools', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(400);
    }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

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


    // /*********************************************************************************************************
    //  *          
    //  *                              METHODES PATCH
    //  * 
    //  ********************************************************************************************************/

    // /*********************** PAS AUTHENTIFIE************************ */

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
                    'name' => 'Piscine test11',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    // /*********************** AUTHENTIFIE EN USER ************************ */

    // Test de l'acces a la route /api/pools/{id} en PATCH authentifie user et owner
    public function testPatchPoolAuthenticatedOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/11',
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
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/pools/{id} en PATCH authentifie Bad owner
    public function testPatchPoolAuthenticatedBadOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/2',
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


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

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



    // /*********************************************************************************************************
    //  *          
    //  *                              METHODES PUT
    //  * 
    //  ********************************************************************************************************/

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


    // /*********************** AUTHENTIFIE EN USER ************************ */

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie Owner
    public function testPutPoolAuthenticatedOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/pools/11',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ],
                'json' => [
                    'name' => 'Piscine test',
                    'description' => 'Description de la piscine',
                    'pricePerDay' => "5500",
                    'location' => 'Paris'
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/pools/{id} en PUT authentifie Bad owner
    public function testPutPoolAuthenticatedBadOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/pools/2',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ],
                'json' => [
                    'owner' => "api/users/2",
                    'name' => 'Piscine test',
                    'description' => 'Description de la piscine',
                    'pricePerDay' => "5500",
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
                    'owner' => 'api/users/1',
                    'name' => 'Piscine test2',
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
                    'owner' => 'api/users/1',
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

    /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de l'acces a la route /api/pools/{id} en DELETE authentifie Bad owner
    public function testDeletePoolAuthentifiedBadOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'DELETE',
            '/api/pools/2',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(403);
    }

    // // Test de l'acces a la route /api/pools/{id} en DELETE authentifie owner
    // public function testDeletePoolAuthentifiedOwner(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'DELETE',
    //         '/api/pools/1',
    //         [
    //             'headers' => [
    //                 'Content-Type' => 'application/ld+json',
    //                 'Authorization' => 'Bearer ' . $this->userToken
    //             ]
    //         ]
    //     );

    //     $this->assertResponseStatusCodeSame(204);
    // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */


    // // Test de l'acces a la route /api/pools/{id} en DELETE authentifie admin
    // public function testDeletePoolAuthorized(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'DELETE',
    //         '/api/pools/3',
    //         [
    //             'headers' => [
    //                 'Content-Type' => 'application/ld+json',
    //                 'Authorization' => 'Bearer ' . $this->adminToken
    //             ]
    //         ]
    //     );

    //     //204 No Content : Si la suppression a réussi, mais que le serveur ne renvoie aucun contenu en réponse.
    //     $this->assertResponseStatusCodeSame(204);
    // }

    // Test de l'acces a la route /api/pools/{id} en DELETE authentifie admin mais avec id inexistant
    public function testDeletePoolAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'DELETE',
            '/api/pools/1111',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
