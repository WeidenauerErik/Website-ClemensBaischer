<?php

namespace App\Controller\Api;

use App\Dto\BookingRequestInput;
use App\Entity\BookingRequest;
use App\Http\PayloadMapper;
use App\Repository\TrailerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

final class BookingRequestController extends AbstractController
{
    #[Route('/api/booking-requests', name: 'api_booking_requests_create', methods: ['POST'])]
    public function __invoke(
        Request $request,
        PayloadMapper $payloadMapper,
        TrailerRepository $trailers,
        EntityManagerInterface $em,
        #[Autowire(service: 'limiter.booking_request')] RateLimiterFactory $bookingRequestLimiter,
    ): JsonResponse {
        $input = $payloadMapper->map($request, BookingRequestInput::class);

        // Honeypot: real visitors never fill this hidden field, bots do.
        // Pretend success so the bot doesn't learn anything, but persist nothing.
        if (null !== $input->website && '' !== $input->website) {
            return $this->json(['status' => 'received'], 201);
        }

        $limit = $bookingRequestLimiter->create($request->getClientIp());
        if (!$limit->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException(600, 'Too many requests. Please try again later.');
        }

        $trailer = $trailers->findOneActiveBySlug($input->trailerSlug);
        if (null === $trailer) {
            throw new UnprocessableEntityHttpException('Unknown trailer.');
        }

        $startDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $input->startDate);
        $endDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $input->endDate);

        if (false === $startDate || false === $endDate) {
            throw new UnprocessableEntityHttpException('Invalid date format, expected YYYY-MM-DD.');
        }

        if ($startDate > $endDate) {
            throw new UnprocessableEntityHttpException('End date must not be before start date.');
        }

        if ($startDate < new \DateTimeImmutable('today')) {
            throw new UnprocessableEntityHttpException('Start date must not be in the past.');
        }

        $bookingRequest = new BookingRequest(
            $trailer,
            trim($input->firstName),
            trim($input->lastName),
            trim($input->email),
            trim($input->phone),
            $startDate,
            $endDate,
            null !== $input->message && '' !== trim($input->message) ? trim($input->message) : null,
            $input->startTime,
            $input->endTime,
        );

        $em->persist($bookingRequest);
        $em->flush();

        return $this->json(['status' => 'received', 'id' => $bookingRequest->getId()], 201);
    }
}
