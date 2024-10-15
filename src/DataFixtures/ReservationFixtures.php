<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            // Récupérer un utilisateur et une piscine aléatoire
            $user = $this->getReference('user' . rand(1, 10)); // Récupérer un utilisateur aléatoire
            $pool = $this->getReference('pool' . rand(1, 10)); // Récupérer une piscine aléatoire

            $reservation = new Reservation();
            $reservation->setLoueur($user);
            $reservation->setPool($pool);
            $reservation->setStartDate(new \DateTime('+ ' . rand(1, 30) . ' days')); // Date dans le futur
            $reservation->setEndDate(new \DateTime('+ ' . rand(1, 30) . ' days')); // Date dans le futur
            $reservation->setApproved(false);

            $manager->persist($reservation);
        }

        // Ne pas oublier de flush
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class, // Déclaration de la dépendance
            PoolFixtures::class,  // Déclaration de la dépendance
        ];
    }
}
