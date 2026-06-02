<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(private UserRepository $userRepo) {}

    #[Route('/users', name: 'api_admin_users', methods: ['GET'])]
    public function users(): JsonResponse
    {
        $users = $this->userRepo->findAllClients();

        return $this->json(array_map(fn($u) => [
            'id'        => (string) $u->getId(),
            'prenom'    => $u->getPrenom(),
            'nom'       => $u->getNom(),
            'email'     => $u->getEmail(),
            'telephone' => $u->getTelephone(),
            'createdAt' => $u->getCreatedAt()?->format('d/m/Y'),
        ], $users));
    }
}
