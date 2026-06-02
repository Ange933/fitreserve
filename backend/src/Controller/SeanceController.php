<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/seances')]
class SeanceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SeanceRepository $seanceRepo,
        private ValidatorInterface $validator,
    ) {}

    // Liste publique : tous les créneaux (disponibles ou non, pour que les clients voient les indisponibles)
    #[Route('', name: 'api_seances_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $seances = $this->seanceRepo->findUpcoming(disponibleOnly: false);
        return $this->json(array_map(fn(Seance $s) => $this->serializeSeance($s), $seances));
    }

    // Liste admin : tous les créneaux (disponibles ou non)
    #[Route('/admin/all', name: 'api_seances_admin_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminList(): JsonResponse
    {
        $seances = $this->seanceRepo->findUpcoming(disponibleOnly: false);
        return $this->json(array_map(fn(Seance $s) => $this->serializeSeance($s), $seances));
    }

    #[Route('/{id}', name: 'api_seances_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $seance = $this->seanceRepo->find($id);
        if (!$seance) {
            return $this->json(['message' => 'Séance introuvable.'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($this->serializeSeance($seance));
    }

    #[Route('', name: 'api_seances_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Données invalides.'], Response::HTTP_BAD_REQUEST);
        }

        $seance = new Seance();
        $this->hydrate($seance, $data);
        $seance->setCreatedBy($this->getUser());

        $errors = $this->validator->validate($seance);
        if (count($errors) > 0) {
            return $this->json(['message' => 'Validation échouée.', 'errors' => $this->formatErrors($errors)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->em->persist($seance);
        $this->em->flush();

        return $this->json($this->serializeSeance($seance), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_seances_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(string $id, Request $request): JsonResponse
    {
        $seance = $this->seanceRepo->find($id);
        if (!$seance) {
            return $this->json(['message' => 'Séance introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $this->hydrate($seance, $data);

        $errors = $this->validator->validate($seance);
        if (count($errors) > 0) {
            return $this->json(['message' => 'Validation échouée.', 'errors' => $this->formatErrors($errors)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->em->flush();
        return $this->json($this->serializeSeance($seance));
    }

    // Bloquer / débloquer un créneau
    #[Route('/{id}/disponibilite', name: 'api_seances_toggle', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleDisponibilite(string $id): JsonResponse
    {
        $seance = $this->seanceRepo->find($id);
        if (!$seance) {
            return $this->json(['message' => 'Séance introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $seance->setDisponible(!$seance->isDisponible());
        $this->em->flush();

        return $this->json($this->serializeSeance($seance));
    }

    #[Route('/{id}', name: 'api_seances_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $id): JsonResponse
    {
        $seance = $this->seanceRepo->find($id);
        if (!$seance) {
            return $this->json(['message' => 'Séance introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($seance);
        $this->em->flush();

        return $this->json(['message' => 'Séance supprimée.'], Response::HTTP_NO_CONTENT);
    }

    private function hydrate(Seance $seance, array $data): void
    {
        if (isset($data['titre'])) $seance->setTitre($data['titre']);
        if (isset($data['description'])) $seance->setDescription($data['description']);
        if (isset($data['dateHeure'])) $seance->setDateHeure(new \DateTime($data['dateHeure']));
        if (isset($data['duree'])) $seance->setDuree((int) $data['duree']);
        if (isset($data['placesMax'])) $seance->setPlacesMax((int) $data['placesMax']);
        if (isset($data['lieu'])) $seance->setLieu($data['lieu']);
    }

    private function serializeSeance(Seance $s): array
    {
        return [
            'id' => (string) $s->getId(),
            'titre' => $s->getTitre(),
            'dateHeure' => $s->getDateHeure()?->format('Y-m-d\TH:i:s'),
            'duree' => $s->getDuree(),
            'placesMax' => $s->getPlacesMax(),
            'placesRestantes' => $s->getPlacesRestantes(),
            'lieu' => $s->getLieu(),
            'disponible' => $s->isDisponible(),
            'description' => $s->getDescription(),
        ];
    }

    private function formatErrors(\Symfony\Component\Validator\ConstraintViolationListInterface $errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }
        return $messages;
    }
}
