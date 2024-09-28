<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Pool;

class PoolFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer l'utilisateur créé dans UserFixtures
        $user = $this->getReference('user1'); // Assurez-vous d'avoir enregistré la référence dans UserFixtures

        for ($i = 1; $i <= 10; $i++) {
            // Récupérer un utilisateur différent pour chaque piscine
            $user = $this->getReference('user' . $i);

            $pool = new Pool();
            $pool->setOwner($user);
            $pool->setName('Piscine ' . $i);
            $pool->setDescription('Description de la piscine ' . $i);
            $pool->setPricePerDay(mt_rand(5000, 7700)); // Prix entre 50 et 200
            $pool->setLocation('Location ' . $i); // Exemple de localisation

            $manager->persist($pool);
            $this->addReference('pool' . $i, $pool); // Ajouter une référence pour chaque piscine
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class, // Déclarez ici la dépendance
        ];
    }
}
