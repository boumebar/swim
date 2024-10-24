<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
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


    // Test de l'accès à la route /api/users pas authentifie
    public function testGetUsersUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);
    }

    // Test de l'accès à la route /api/users/{id} pas authentifie
    public function testGetUserUnauthorized(): void
    {
        // Test d’accès à la route sans authentification
        static::createClient()->request('GET', '/api/users/1');
        $this->assertResponseStatusCodeSame(401);
    }


    // /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de l'accès à la route en GET /api/users en tant qu'utilisateur authentifié
    public function testGetUsersAuthenticatedUserForbidden(): void
    {
        // Effectuer une requête GET à /api/users avec le token
        static::createClient()->request('GET', '/api/users', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    // // // Test de l'accès à la route en GET /api/pools/{id} en tant qu'utilisateur authentifié
    // // public function testGetPoolAuthenticated(): void
    // // {
    // //     // Effectuer une requête GET à /api/pools avec le token
    // //     static::createClient()->request('GET', '/api/pools/1', [
    // //         'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
    // //     ]);

    // //     $this->assertResponseStatusCodeSame(200);
    // // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */


    // Test de l'accès à la route en GET /api/users en tant qu'administrateur authentifié
    public function testGetUsersAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/users avec le token
        static::createClient()->request('GET', '/api/users', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à la route en GET /api/users/{id} en tant qu'administrateur authentifié
    public function testGetUserAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/users avec le token
        static::createClient()->request('GET', '/api/users/1', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à une reservation inexistante en tant qu'administrateur
    public function testGetNonExistentUserAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/users avec le token
        static::createClient()->request('GET', '/api/users/1000', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    // /*********************************************************************************************************
    //  *          
    //  *                              METHODES POST
    //  * 
    //  ********************************************************************************************************/


    // /*********************** PAS AUTHENTIFIE************************ */


    // // Test de l'acces a la route /api/users en POST pas authentifie
    // public function testPostUsersUnauthorized(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'POST',
    //         '/api/users',
    //         [
    //             'headers' => ['Content-Type' => 'application/json'],
    //             'json' => [] // Envoyer un JSON vide ou un corps conforme
    //         ]
    //     );
    //     $this->assertResponseStatusCodeSame(401);
    // }

    // // /*********************** AUTHENTIFIE EN USER ************************ */


    // // Test de la création d'une reservation avec des données valides en tant qu'utilisateur
    // public function testCreateReservationWithValidData(): void
    // {
    //     // Données valides pour la création d'une piscine
    //     $data = [
    //         'startDate' => "2025-10-15 08:57:44",
    //         'endDate' => "2025-11-15 08:57:44",
    //         'loueur' => "api/users/2",
    //         'pool' => "api/pools/1",
    //     ];

    //     // Effectuer une requête POST pour créer une reservation
    //     static::createClient()->request('POST', '/api/reservations', [
    //         'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
    //         'json' => $data,
    //     ]);

    //     // Vérifier que la réponse est 201 Created
    //     $this->assertResponseStatusCodeSame(201);
    // }

    // // Test de la création d'une reservation avec des données invalides et les droits 
    // public function testCreateReservationWithInvalidData(): void
    // {
    //     // Données valides pour la création d'une reservation
    //     $data = [
    //         'loueur' => "api/users/2",
    //         'pool' => "api/pools/1"
    //     ];

    //     // Effectuer une requête POST pour créer une reservation
    //     static::createClient()->request('POST', '/api/reservations', [
    //         'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
    //         'json' => $data,
    //     ]);

    //     // Vérifier que la réponse est 422 Manque des données
    //     $this->assertResponseStatusCodeSame(422);
    // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // // Test de la création d'une user avec des données valides 
    // public function testCreateUserWithValidDataAdmin(): void
    // {
    //     // Données valides pour la création d'une user
    //     $data = [
    //         'username' => "boume",
    //         'email' => "boume@boume.com",
    //         'password' => "password"
    //     ];

    //     // Effectuer une requête POST pour créer une user
    //     static::createClient()->request('POST', '/api/users', [
    //         'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
    //         'json' => $data,
    //     ]);

    //     // Vérifier que la réponse est 201 Created
    //     $this->assertResponseStatusCodeSame(201);
    // }

    // Test de la création d'une user avec des données invalides avec les droits 
    public function testCreateUserWithInvalidDataAdmin(): void
    {
        // Données valides pour la création d'une user
        $data = [
            'username' => "boume",
        ];

        // Effectuer une requête POST pour créer une user
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }

    // tester email not blank

    // tester username not blank

    // tester password not blank

    //  Tester pour les mot de passe 8 caractere et majuscule minuscule et un chiffre


    // tester pour l'email valide 

    // tester email invalide 

    // tester username min 2 caracteres valide 

    // tester username min 2 caracteres invalide



    /********************************************************************************************************
    //  *          
    //  *                              METHODES PATCH
    //  * 
    //  ********************************************************************************************************/

    // /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/users/{id} en PATCH pas authentifie
    public function testPatchUserUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/users/1',
            [
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
                'json' => [
                    'name' => 'Nouvel user100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    // // /*********************** AUTHENTIFIE EN USER ************************ */

    // // // Test de l'acces a la route /api/pools/{id} en PATCH authentifie user
    // // public function testPatchPoolAuthenticatedUnauthorized(): void
    // // {
    // //     // Test d'accès à la route sans authentification avec du JSON
    // //     static::createClient()->request(
    // //         'PATCH',
    // //         '/api/pools/1',
    // //         [
    // //             'headers' => [
    // //                 'Content-Type' => 'application/merge-patch+json',
    // //                 'Authorization' => 'Bearer ' . $this->userToken
    // //             ],
    // //             'json' => [
    // //                 'name' => 'Piscine test100',
    // //             ] // Envoyer un JSON vide ou un corps conforme
    // //         ]
    // //     );
    // //     $this->assertResponseStatusCodeSame(403);
    // // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/users/{id} en PATCH authentifie admin
    public function testPatchUserAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/users/7',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'username' => 'nouveau nom',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/users/{id} en PATCH authentifie admin mais avec id inexistant
    public function testPatchUserAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/users/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'username' => 'nouveau nom',
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

    // /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/users/{id} en PUT pas authentifie
    public function testPutUserUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/users/4',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
                'json' => [
                    'username' => "boume",
                    'email' => "boume@boume.com",
                    'password' => "password"
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    // // /*********************** AUTHENTIFIE EN USER ************************ */

    // // // Test de l'acces a la route /api/pools/{id} en PUT authentifie user
    // // public function testPutPoolAuthenticatedUnauthorized(): void
    // // {
    // //     // Test d'accès à la route sans authentification avec du JSON
    // //     static::createClient()->request(
    // //         'PUT',
    // //         '/api/pools/1',
    // //         [
    // //             'headers' => [
    // //                 'Content-Type' => 'application/ld+json',
    // //                 'Authorization' => 'Bearer ' . $this->userToken
    // //             ],
    // //             'json' => [
    // //                 'owner' => 1,
    // //                 'name' => 'Piscine test',
    // //                 'description' => 'Description de la piscine',
    // //                 'pricePerDay' => 50.0,
    // //                 'location' => 'Paris'
    // //             ] // Envoyer un JSON vide ou un corps conforme
    // //         ]
    // //     );
    // //     $this->assertResponseStatusCodeSame(403);
    // // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/users/{id} en PUT authentifie admin
    public function testPutUserAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/users/5',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'username' => "boume",
                    'email' => "boume@boumez.com",
                    'password' => "password"
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/users/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutUserAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/users/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'username' => "boume",
                    'email' => "boume@boume.com",
                    'password' => "password"
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }

    // Test de l'acces a la route /api/users/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutUserAuthorizedWithInvalidData(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/users/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'username' => "boume",
                    'email' => "boume@boume.com"
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(422);
    }

    // /*********************************************************************************************************
    //  *          
    //  *                              METHODES DELETE
    //  * 
    //  ********************************************************************************************************/

    // /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/users/{id} en DELETE pas authentifie
    public function testDeleteUserUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('DELETE', '/api/users/9');
        $this->assertResponseStatusCodeSame(401);
    }


    // /*********************** AUTHENTIFIE EN USER ************************ */


    // // // Test de l'acces a la route /api/pools/{id} en DELETE authentifie admin
    // // public function testDeletePoolUnAuthorizedUser(): void
    // // {
    // //     // Test d'accès à la route sans authentification avec du JSON
    // //     static::createClient()->request(
    // //         'DELETE',
    // //         '/api/pools/2',
    // //         [
    // //             'headers' => [
    // //                 'Content-Type' => 'application/ld+json',
    // //                 'Authorization' => 'Bearer ' . $this->userToken
    // //             ]
    // //         ]
    // //     );

    // //     $this->assertResponseStatusCodeSame(403);
    // // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/users/{id} en DELETE authentifie admin
    // public function testDeleteUserAuthorized(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'DELETE',
    //         '/api/users/6',
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

    // Test de l'acces a la route /api/users/{id} en DELETE authentifie admin mais avec id inexistant
    public function testDeleteUserAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'DELETE',
            '/api/users/1111',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }



    // /*********************************************************************************************************
    //  *          
    //  *                              ROUTE ME 
    //  * 
    //  ********************************************************************************************************/

    // Test de l'acces a la route /api/me
    public function testMeRouteAuthenticated(): void
    {
        // Effectuer une requête authentifiée avec un token valide
        static::createClient()->request(
            'GET',
            '/api/me',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->userToken, // Assurez-vous que $this->userToken est défini
                ],
            ]
        );

        // Vérifier que la requête réussit
        $this->assertResponseIsSuccessful();

        // Vérifier que la réponse contient les informations de l'utilisateur
        $this->assertJsonContains([
            'email' => 'user1@example.com',
            'username' => 'user1',
        ]);
    }
}
