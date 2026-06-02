<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Seance;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@fitreserve.fr');
        $admin->setNom('Durand');
        $admin->setPrenom('Marc');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        // Clients
        $clients = [];
        $clientData = [
            ['sophie.martin@email.fr', 'Martin', 'Sophie', '0612345678'],
            ['thomas.bernard@email.fr', 'Bernard', 'Thomas', '0687654321'],
            ['emma.dubois@email.fr', 'Dubois', 'Emma', '0698112233'],
        ];

        foreach ($clientData as [$email, $nom, $prenom, $tel]) {
            $client = new User();
            $client->setEmail($email);
            $client->setNom($nom);
            $client->setPrenom($prenom);
            $client->setTelephone($tel);
            $client->setPassword($this->hasher->hashPassword($client, 'User1234!'));
            $manager->persist($client);
            $clients[] = $client;
        }

        // Séances — 4 semaines, 4 créneaux/jour, pas de dimanche
        $horaires = ['09:00', '11:00', '13:00', '15:00', '17:00'];
        $description = 'Séance personnalisée avec Marc. L\'entraînement sera défini ensemble sur place selon vos objectifs et votre forme du jour.';
        $seances = [];

        $paris = new \DateTimeZone('Europe/Paris');
        $start = new \DateTime('tomorrow', $paris);
        $end   = new \DateTime('+4 weeks', $paris);

        for ($day = clone $start; $day <= $end; $day->modify('+1 day')) {
            // 0 = dimanche
            if ((int) $day->format('w') === 0) {
                continue;
            }

            foreach ($horaires as $heure) {
                $dateHeure = \DateTime::createFromFormat('Y-m-d H:i', $day->format('Y-m-d') . ' ' . $heure, $paris);
                $seance = new Seance();
                $seance->setTitre('Séance coaching');
                $seance->setDescription($description);
                $seance->setDateHeure($dateHeure);
                $seance->setDuree(60);
                $seance->setPlacesMax(1);
                $seance->setLieu('Salle de sport Paris 8e');
                $seance->setCreatedBy($admin);
                $manager->persist($seance);
                $seances[] = $seance;
            }
        }

        // Réservations
        $reservationData = [
            [$clients[0], $seances[0]],
            [$clients[0], $seances[1]],
            [$clients[1], $seances[0]],
            [$clients[1], $seances[2]],
            [$clients[2], $seances[3]],
        ];

        foreach ($reservationData as [$client, $seance]) {
            $reservation = new Reservation();
            $reservation->setUser($client);
            $reservation->setSeance($seance);
            $reservation->setStatut('confirmee');
            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
