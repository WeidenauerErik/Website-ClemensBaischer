<?php

namespace App\Controller\Api;

use App\Entity\Trailer;
use App\Repository\TrailerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/trailers')]
final class TrailerController extends AbstractController
{
    #[Route('', name: 'api_trailers_list', methods: ['GET'])]
    public function list(TrailerRepository $trailers): JsonResponse
    {
        return $this->json(array_map(
            $this->serializeTrailer(...),
            $trailers->findAllActive(),
        ));
    }

    #[Route('/{slug}', name: 'api_trailers_show', methods: ['GET'])]
    public function show(string $slug, TrailerRepository $trailers): JsonResponse
    {
        $trailer = $trailers->findOneActiveBySlug($slug);
        if (null === $trailer) {
            throw $this->createNotFoundException('Trailer not found.');
        }

        return $this->json($this->serializeTrailer($trailer));
    }

    /** @return array<string, mixed> */
    private function serializeTrailer(Trailer $trailer): array
    {
        return [
            'id' => $trailer->getId(),
            'name' => $trailer->getName(),
            'slug' => $trailer->getSlug(),
            'description' => $trailer->getDescription(),
            'imageUrl' => null !== $trailer->getImageFilename()
                ? '/uploads/trailers/'.$trailer->getImageFilename()
                : null,
        ];
    }
}
