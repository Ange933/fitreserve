<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ReservationRepository $reservationRepo,
        private SeanceRepository $seanceRepo,
    ) {}

    #[Route('/reservations', name: 'api_reservations_list', methods: ['GET'])]
    public function myReservations(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $reservations = $this->reservationRepo->findByUser($user);

        return $this->json(array_map(fn(Reservation $r) => $this->serialize($r, false), $reservations));
    }

    #[Route('/reservations', name: 'api_reservations_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $seanceId = $data['seanceId'] ?? null;

        if (!$seanceId) {
            return $this->json(['message' => 'L\'identifiant de la séance est requis.'], Response::HTTP_BAD_REQUEST);
        }

        $seance = $this->seanceRepo->find($seanceId);
        if (!$seance) {
            return $this->json(['message' => 'Séance introuvable.'], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $this->getUser();

        $existing = $this->reservationRepo->findActiveForSeanceAndUser($seanceId, $user);
        if ($existing) {
            return $this->json(['message' => 'Vous avez déjà réservé cette séance.'], Response::HTTP_CONFLICT);
        }

        if ($seance->getPlacesRestantes() <= 0) {
            return $this->json(['message' => 'Cette séance est complète.'], Response::HTTP_CONFLICT);
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setSeance($seance);
        $reservation->setStatut('confirmee');

        $this->em->persist($reservation);
        $this->em->flush();

        return $this->json($this->serialize($reservation, false), Response::HTTP_CREATED);
    }

    #[Route('/reservations/{id}', name: 'api_reservations_cancel', methods: ['DELETE'])]
    public function cancel(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $reservation = $this->reservationRepo->find($id);

        if (!$reservation) {
            return $this->json(['message' => 'Réservation introuvable.'], Response::HTTP_NOT_FOUND);
        }

        if ($reservation->getUser() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        $reservation->setStatut('annulee');
        $this->em->flush();

        return $this->json(['message' => 'Réservation annulée.']);
    }

    #[Route('/admin/reservations', name: 'api_admin_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function allReservations(): JsonResponse
    {
        $reservations = $this->reservationRepo->findAll();

        return $this->json(array_map(fn(Reservation $r) => $this->serialize($r, true), $reservations));
    }

    private function serialize(Reservation $r, bool $withUser): array
    {
        $data = [
            'id' => (string) $r->getId(),
            'statut' => $r->getStatut(),
            'createdAt' => $r->getCreatedAt()?->format('c'),
            'seance' => [
                'id' => (string) $r->getSeance()->getId(),
                'titre' => $r->getSeance()->getTitre(),
                'dateHeure' => $r->getSeance()->getDateHeure()?->format('Y-m-d\TH:i:s'),
                'duree' => $r->getSeance()->getDuree(),
                'lieu' => $r->getSeance()->getLieu(),
            ],
        ];

        if ($withUser) {
            $data['user'] = [
                'id' => (string) $r->getUser()->getId(),
                'email' => $r->getUser()->getEmail(),
                'nom' => $r->getUser()->getNom(),
                'prenom' => $r->getUser()->getPrenom(),
                'telephone' => $r->getUser()->getTelephone(),
            ];
        }

        return $data;
    }
}
