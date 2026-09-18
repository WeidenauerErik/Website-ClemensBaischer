<?php

namespace App\Controller\Admin;

use App\Dto\BookingInput;
use App\Entity\AdminUser;
use App\Entity\Booking;
use App\Enum\BookingType;
use App\Http\PayloadMapper;
use App\Repository\BookingRepository;
use App\Repository\TrailerRepository;
use App\Service\BookingManager;
use App\Service\BookingOverlapException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/api')]
#[IsGranted('ROLE_ADMIN')]
final class BookingApiController extends AbstractController
{
    #[Route('/trailers/{trailerId}/bookings', name: 'admin_api_bookings_list', methods: ['GET'], requirements: ['trailerId' => '\d+'])]
    public function list(
        int $trailerId,
        Request $request,
        TrailerRepository $trailers,
        BookingRepository $bookings,
    ): JsonResponse {
        $trailer = $trailers->find($trailerId);
        if (null === $trailer) {
            throw $this->createNotFoundException('Trailer not found.');
        }

        $from = $this->parseDate($request->query->get('from')) ?? new \DateTimeImmutable('first day of this month');
        $to = $this->parseDate($request->query->get('to')) ?? $from->modify('+3 months');

        return $this->json(array_map(
            $this->serializeBooking(...),
            $bookings->findForTrailerInRange($trailer, $from, $to),
        ));
    }

    #[Route('/trailers/{trailerId}/bookings', name: 'admin_api_bookings_create', methods: ['POST'], requirements: ['trailerId' => '\d+'])]
    public function create(
        int $trailerId,
        Request $request,
        PayloadMapper $payloadMapper,
        TrailerRepository $trailers,
        BookingManager $bookingManager,
    ): JsonResponse {
        $input = $payloadMapper->map($request, BookingInput::class);

        $trailer = $trailers->find($trailerId);
        if (null === $trailer) {
            throw $this->createNotFoundException('Trailer not found.');
        }

        [$start, $end] = $this->parseRange($input->startDate, $input->endDate);

        /** @var AdminUser $admin */
        $admin = $this->getUser();

        try {
            $booking = $bookingManager->createBooking(
                $trailer,
                $start,
                $end,
                BookingType::from($input->type),
                $admin,
                $input->note,
                null,
                $input->customerFirstName,
                $input->customerLastName,
                $input->customerEmail,
                $input->customerPhone,
            );
        } catch (BookingOverlapException $e) {
            throw new ConflictHttpException($e->getMessage());
        }

        return $this->json($this->serializeBooking($booking), 201);
    }

    #[Route('/bookings/{id}', name: 'admin_api_bookings_update', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function update(
        Booking $booking,
        Request $request,
        PayloadMapper $payloadMapper,
        BookingManager $bookingManager,
    ): JsonResponse {
        $input = $payloadMapper->map($request, BookingInput::class);

        [$start, $end] = $this->parseRange($input->startDate, $input->endDate);

        try {
            $booking = $bookingManager->updateBooking(
                $booking,
                $start,
                $end,
                BookingType::from($input->type),
                $input->note,
                $input->customerFirstName,
                $input->customerLastName,
                $input->customerEmail,
                $input->customerPhone,
            );
        } catch (BookingOverlapException $e) {
            throw new ConflictHttpException($e->getMessage());
        }

        return $this->json($this->serializeBooking($booking));
    }

    #[Route('/bookings/{id}', name: 'admin_api_bookings_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Booking $booking, BookingManager $bookingManager): JsonResponse
    {
        $bookingManager->deleteBooking($booking);

        return $this->json(null, 204);
    }

    /** @return array{0: \DateTimeImmutable, 1: \DateTimeImmutable} */
    private function parseRange(string $startDate, string $endDate): array
    {
        $start = $this->parseDate($startDate);
        $end = $this->parseDate($endDate);

        if (null === $start || null === $end) {
            throw new UnprocessableEntityHttpException('Invalid date format, expected YYYY-MM-DD.');
        }

        if ($start > $end) {
            throw new UnprocessableEntityHttpException('End date must not be before start date.');
        }

        return [$start, $end];
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

    /** @return array<string, mixed> */
    private function serializeBooking(Booking $booking): array
    {
        $sourceRequest = $booking->getSourceRequest();

        return [
            'id' => $booking->getId(),
            'start' => $booking->getStartDate()->format('Y-m-d'),
            'end' => $booking->getEndDate()->format('Y-m-d'),
            'type' => $booking->getType()->value,
            'note' => $booking->getNote(),
            'customerFirstName' => $booking->getCustomerFirstName(),
            'customerLastName' => $booking->getCustomerLastName(),
            'customerEmail' => $booking->getCustomerEmail(),
            'customerPhone' => $booking->getCustomerPhone(),
            'createdBy' => $booking->getCreatedBy()?->getFullName(),
            'sourceRequest' => null !== $sourceRequest ? [
                'id' => $sourceRequest->getId(),
                'customerName' => $sourceRequest->getFullName(),
                'email' => $sourceRequest->getEmail(),
                'phone' => $sourceRequest->getPhone(),
                'message' => $sourceRequest->getMessage(),
            ] : null,
        ];
    }
}
