<?php

namespace App\Tests;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ReservationTest extends ApiTestCase
{

    /****************************************************************************************************
     * 
     *              INITIALISATION DES TOKENS EN USER ET ADMIN
     * 
     *******************************************************************************************************/

    private $userToken;
    private $adminToken;
    private $reservation;
    private EntityManagerInterface $entityManager;

    //Initialisation et generation de token en user et en admin 
    protected function setUp(): void
    {
        $this->reservation = new Reservation();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        parent::setUp();

        $client = static::createClient();

        // Créer un utilisateur normal
        $userData = [
            'email' => 'user2@user.com',
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


    // Test de l'accès à la route /api/reservations pas authentifie
    public function testGetReservationsUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('GET', '/api/reservations');
        $this->assertResponseStatusCodeSame(401);
    }

    // Test de l'accès à la route /api/reservations/{id} pas authentifie
    public function testGetReservationUnauthorized(): void
    {
        // Test d’accès à la route sans authentification
        static::createClient()->request('GET', '/api/reservations/1');
        $this->assertResponseStatusCodeSame(401);
    }


    /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de l'accès à la route en GET /api/reservations en tant qu'utilisateur authentifié
    public function testGetReservationsAuthenticatedUserForbidden(): void
    {
        // Effectuer une requête GET à /api/reservations avec le token
        static::createClient()->request('GET', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    // Test de l'acces a la route /api/reservations/{id} en GET authentifie user et owner
    public function testGetReservationAuthenticatedOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'GET',
            '/api/reservations/2',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/reservations/{id} en GET authentifie user et bad owner
    public function testGetReservationAuthenticatedBadOwner(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'GET',
            '/api/reservations/1',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(403);
    }



    // /*********************** AUTHENTIFIE EN ADMIN ************************ */


    // Test de l'accès à la route en GET /api/reservations en tant qu'administrateur authentifié
    public function testGetReservationsAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à la route en GET /api/reservations/{id} en tant qu'administrateur authentifié
    public function testGetReservationAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/reservations/1', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'accès à une reservation inexistante en tant qu'administrateur
    public function testGetNonExistentReservationAdminAuthenticated(): void
    {
        // Effectuer une requête GET à /api/pools avec le token
        static::createClient()->request('GET', '/api/reservations/1000', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    /*********************************************************************************************************
     *          
     *                              METHODES POST
     * 
     ********************************************************************************************************/


    // /*********************** PAS AUTHENTIFIE************************ */


    // Test de l'acces a la route /api/reservations en POST pas authentifie
    public function testPostReservationsUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'POST',
            '/api/reservations',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }

    // /*********************** AUTHENTIFIE EN USER ************************ */


    // Test de la création d'une reservation avec des données valides en tant qu'utilisateur
    public function testCreateReservationWithValidData(): void
    {
        // Données valides pour la création d'une piscine
        $data = [
            'startDate' => "2025-10-15 08:57:44",
            'endDate' => "2025-11-15 08:57:44",
            'pool' => "api/pools/1",
        ];

        // Effectuer une requête POST pour créer une reservation
        static::createClient()->request('POST', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 201 Created
        $this->assertResponseStatusCodeSame(201);
    }

    // Test de la création d'une reservation avec des données invalides et les droits 
    public function testCreateReservationWithInvalidData(): void
    {
        // Données valides pour la création d'une reservation
        $data = [
            'pool' => "api/pools/1"
        ];

        // Effectuer une requête POST pour créer une reservation
        static::createClient()->request('POST', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->userToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Manque des données
        $this->assertResponseStatusCodeSame(422);
    }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de la création d'une reservation avec des données valides 
    public function testCreateReservationWithValidDataAdmin(): void
    {
        // Données valides pour la création d'une reservation
        $data = [
            'startDate' => "2025-10-12 08:57:44",
            'endDate' => "2025-11-13 08:57:44",
            'pool' => "api/pools/5",
        ];

        // Effectuer une requête POST pour créer une reservation
        static::createClient()->request('POST', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 201 Created
        $this->assertResponseStatusCodeSame(201);
    }

    // Test de la création d'une reservation avec des données invalides avec les droits 
    public function testCreateReservationWithInvalidDataAdmin(): void
    {
        // Données valides pour la création d'une reservation
        $data = [
            'owner' => 'api/users/1',
            'name' => 'Piscine test',
        ];

        // Effectuer une requête POST pour créer une reservation
        static::createClient()->request('POST', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
    }


    // Test de la création d'une reservation avec des données valides et que le champ isApproved est faux
    public function testDefaultIsApproved()
    {
        // Données valides pour la création d'une réservation
        $data = [
            'startDate' => "2025-11-12 08:57:44",
            'endDate' => "2025-12-13 08:57:44",
            'pool' => "/api/pools/5",    // Correction : lien URI pour la piscine
        ];

        // Effectuer une requête POST pour créer une réservation
        $response = static::createClient()->request('POST', '/api/reservations', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $data,
        ]);

        // Vérifier que la réponse est un succès (statut 201)
        $this->assertResponseStatusCodeSame(201);

        // Récupérer les données de la réponse
        $responseData = $response->toArray();

        // Effectuer une requête GET pour récupérer la réservation fraîchement créée
        $reservationIri = $responseData['@id'];
        $reservation = static::createClient()->request('GET', $reservationIri, [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
        ])->toArray();

        // La valeur par défaut de isApproved doit être false
        $this->assertFalse($reservation['approved']);
    }



    /********************************************************************************************************
    //  *          
    //  *                              METHODES PATCH
    //  * 
    //  ********************************************************************************************************/

    // /*********************** PAS AUTHENTIFIE************************ */

    // Test de l'acces a la route /api/reservations/{id} en PATCH pas authentifie
    public function testPatchReservationUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/reservations/1',
            [
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
                'json' => [
                    'name' => 'Reservation test100',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    // /*********************** AUTHENTIFIE EN USER ************************ */

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


    // Test de l'acces a la route /api/reservations/{id} en PATCH authentifie user et owner verifie la date updated et differente de created
    public function testPatchReservationAuthenticatedOwnerUpdatedNotCreated(): void
    {
        // Récupérer la reservation avant la mise à jour
        $reservationId = 2; // ID de la piscine à tester
        $originalReservation = $this->entityManager->getRepository(Reservation::class)->find($reservationId);


        // Obtenir les valeurs avant la mise à jour
        $createdAtBefore = $originalReservation->getCreatedAt();
        $updatedAtBefore = $originalReservation->getUpdatedAt();

        // Effectuer la requête PATCH
        $client = static::createClient();
        $client->request(
            'PATCH',
            '/api/reservations/' . $reservationId,
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->userToken
                ],
                'json' => [
                    'pool' => 'api/pools/3',
                ]
            ]
        );

        // Récupérer la reservation mise à jour
        $updatedReservation = $this->entityManager->getRepository(Reservation::class)->find($reservationId);


        // Vérifier que updatedAt est différent de createdAt
        $this->assertNotEquals($createdAtBefore, $updatedReservation->getUpdatedAt(), 'Le champ updatedAt doit être différent du champ createdAt après modification.');
    }

    // Vérifier que createdAt ne change jamais
    public function testCreatedAtNeverChanges(): void
    {
        // Récupérer la reservation existante
        $reservation = $this->entityManager->getRepository(Reservation::class)->find(2);
        // Stocker la valeur initiale de createdAt
        $initialCreatedAt = $reservation->getCreatedAt();

        // Effectuer plusieurs modifications
        $reservation->setEndDate(new \DateTimeImmutable('2024-12-25 10:32:58'));
        $this->entityManager->flush();

        $reservation->setEndDate(new \DateTimeImmutable('2024-12-25 10:32:58'));
        $this->entityManager->flush();
        // Vérifier que createdAt n'a pas changé
        $this->assertEquals(
            $initialCreatedAt,
            $reservation->getCreatedAt(),
            'createdAt ne doit pas être modifié même après plusieurs mises à jour'
        );
    }

    // Vérifier que updatedAt ne change pas sans modification
    public function testUpdatedAtDoesNotChangeWithoutModification(): void
    {
        // Récupérer la piscine existante
        $reservation = $this->entityManager->getRepository(Reservation::class)->find(2);

        // Stocker la valeur initiale de updatedAt
        $initialUpdatedAt = $reservation->getUpdatedAt();

        // Appeler flush sans modification
        $this->entityManager->flush();

        // Vérifier que updatedAt n'a pas changé
        $this->assertEquals(
            $initialUpdatedAt,
            $reservation->getUpdatedAt(),
            'updatedAt ne doit pas être modifié si aucune modification n\'est faite'
        );
    }



    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // Test de l'acces a la route /api/reservations/{id} en PATCH authentifie admin
    public function testPatchReservationAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/reservations/1',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'pool' => 'api/pools/3',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/reservations/{id} en PATCH authentifie admin mais avec id inexistant
    public function testPatchReservationAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PATCH',
            '/api/reservations/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'pool' => 'api/pools/1',
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

    // Test de l'acces a la route /api/reservations/{id} en PUT pas authentifie
    public function testPutReservationUnauthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/reservations/1',
            [
                'headers' => ['Content-Type' => 'application/ld+json'],
                'json' => [
                    'pool' => 'api/pools/1',
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }


    // /*********************** AUTHENTIFIE EN USER ************************ */

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

    // Test de l'acces a la route /api/reservations/{id} en PUT authentifie admin
    public function testPutReservationAuthorized(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/reservations/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'startDate' => "2025-12-12 08:57:44",
                    'endDate' => "2025-12-13 08:57:44",
                    'pool' => "api/pools/2",
                ] // Envoyer un JSON vide ou un corps conforme
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }

    // Test de l'acces a la route /api/reservations/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutReservationAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/reservations/22221',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'startDate' => "2025-12-12 08:57:44",
                    'endDate' => "2025-12-13 08:57:44",
                    'loueur' => "api/users/5",
                    'pool' => "api/pools/2"
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(404);
    }

    // Test de l'acces a la route /api/reservations/{id} en PUT authentifie admin mais avec id inexistant
    public function testPutReservationAuthorizedWithInvalidData(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'PUT',
            '/api/reservations/1',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ],
                'json' => [
                    'pool' => 'api/pools/1',
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

    // Test de l'acces a la route /api/pools/{id} en DELETE pas authentifie
    public function testDeleteReservationUnauthorized(): void
    {
        // Test d'accès à la route sans authentification
        static::createClient()->request('DELETE', '/api/reservations/1');
        $this->assertResponseStatusCodeSame(401);
    }


    /*********************** AUTHENTIFIE EN USER ************************ */


    // // Test de l'acces a la route /api/pools/{id} en DELETE authentifie admin
    // public function testDeletePoolUnAuthorizedUser(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'DELETE',
    //         '/api/pools/2',
    //         [
    //             'headers' => [
    //                 'Content-Type' => 'application/ld+json',
    //                 'Authorization' => 'Bearer ' . $this->userToken
    //             ]
    //         ]
    //     );

    //     $this->assertResponseStatusCodeSame(403);
    // }


    // /*********************** AUTHENTIFIE EN ADMIN ************************ */

    // // Test de l'acces a la route /api/reservations/{id} en DELETE authentifie admin
    // public function testDeleteReservationAuthorized(): void
    // {
    //     // Test d'accès à la route sans authentification avec du JSON
    //     static::createClient()->request(
    //         'DELETE',
    //         '/api/reservations/3',
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

    // Test de l'acces a la route /api/reservations/{id} en DELETE authentifie admin mais avec id inexistant
    public function testDeleteReservationAuthorizedWithNonExistentId(): void
    {
        // Test d'accès à la route sans authentification avec du JSON
        static::createClient()->request(
            'DELETE',
            '/api/reservations/1111',
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->adminToken
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }



    /*********************************************************************************************************
     *          
     *                              AUTRES
     * 
     ********************************************************************************************************/

    // Verifie que createdAt et pas Null
    public function testCreatedAtNotNull()
    {
        // Vérifie que createdAt est initialisé
        $this->assertNotNull($this->reservation->getCreatedAt(), 'Le champ createdAt ne doit pas être nul');
    }

    // Verifie que updatedAt et pas Null
    public function testUpdatedAtNotNull()
    {
        // Vérifie que updatedAt est initialisé
        $this->assertNotNull($this->reservation->getUpdatedAt(), 'Le champ createdAt ne doit pas être nul');
    }


    // Verifie que a la creation d'une reservation le createdAt et updatedAt sont identiques
    public function testCreatedAtAndUpdatedAtAreTheSame()
    {
        $this->assertEquals(
            abs($this->reservation->getCreatedAt()->getTimestamp() - $this->reservation->getUpdatedAt()->getTimestamp()) < 1,
            'createdAt et updatedAt doivent être presque égaux lors de la création'
        );
    }
}
