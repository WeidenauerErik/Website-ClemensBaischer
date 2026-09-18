<?php

namespace App\Controller\Api;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\TrailerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AvailabilityController extends AbstractController
{
    #[Route('/api/trailers/{slug}/availability', name: 'api_trailers_availability', methods: ['GET'])]
    public function __invoke(
        string $slug,
        Request $request,
        TrailerRepository $trailers,
        BookingRepository $bookings,
    ): JsonResponse {
        $trailer = $trailers->findOneActiveBySlug($slug);
        if (null === $trailer) {
            throw $this->createNotFoundException('Trailer not found.');
        }

        $from = $this->parseDate($request->query->get('from')) ?? new \DateTimeImmutable('today');
        $to = $this->parseDate($request->query->get('to')) ?? $from->modify('+6 months');

        if ($from > $to) {
            throw new BadRequestHttpException('"from" must not be after "to".');
        }

        $busy = array_map(
            static fn (Booking $booking): array => [
                'start' => $booking->getStartDate()->format('Y-m-d'),
                'end' => $booking->getEndDate()->format('Y-m-d'),
            ],
            $bookings->findForTrailerInRange($trailer, $from, $to),
        );

        return $this->json([
            'trailerSlug' => $trailer->getSlug(),
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'busy' => $busy,
        ]);
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (false === $date) {
            throw new BadRequestHttpException(sprintf('Invalid date "%s", expected format YYYY-MM-DD.', $value));
        }

        return $date;
    }
}
