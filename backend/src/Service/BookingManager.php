<?php

namespace App\Service;

use App\Entity\AdminUser;
use App\Entity\Booking;
use App\Entity\BookingRequest;
use App\Entity\Trailer;
use App\Enum\BookingRequestStatus;
use App\Enum\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Owns the invariant that two bookings for the same trailer never overlap,
 * and the state transitions from a customer BookingRequest to a confirmed Booking.
 */
final class BookingManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BookingRepository $bookingRepository,
    ) {
    }

    public function createBooking(
        Trailer $trailer,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        BookingType $type,
        AdminUser $createdBy,
        ?string $note = null,
        ?BookingRequest $sourceRequest = null,
        ?string $customerFirstName = null,
        ?string $customerLastName = null,
        ?string $customerEmail = null,
        ?string $customerPhone = null,
        ?string $startTime = null,
        ?string $endTime = null,
    ): Booking {
        $this->assertNoOverlap($trailer, $start, $end);

        $booking = new Booking($trailer, $start, $end, $type, $createdBy, $startTime, $endTime);
        $booking->setNote($note);
        $booking->setCustomerFirstName($customerFirstName);
        $booking->setCustomerLastName($customerLastName);
        $booking->setCustomerEmail($customerEmail);
        $booking->setCustomerPhone($customerPhone);
        if (null !== $sourceRequest) {
            $booking->setSourceRequest($sourceRequest);
        }

        $this->em->persist($booking);
        $this->em->flush();

        return $booking;
    }

    public function updateBooking(
        Booking $booking,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        BookingType $type,
        ?string $note,
        ?string $customerFirstName = null,
        ?string $customerLastName = null,
        ?string $customerEmail = null,
        ?string $customerPhone = null,
        ?string $startTime = null,
        ?string $endTime = null,
    ): Booking {
        $this->assertNoOverlap($booking->getTrailer(), $start, $end, $booking);

        $booking->setStartDate($start)->setEndDate($end)->setType($type)->setNote($note);
        $booking->setStartTime($startTime)->setEndTime($endTime);
        $booking->setCustomerFirstName(BookingType::Customer === $type ? $customerFirstName : null);
        $booking->setCustomerLastName(BookingType::Customer === $type ? $customerLastName : null);
        $booking->setCustomerEmail(BookingType::Customer === $type ? $customerEmail : null);
        $booking->setCustomerPhone(BookingType::Customer === $type ? $customerPhone : null);
        $this->em->flush();

        return $booking;
    }

    public function deleteBooking(Booking $booking): void
    {
        $this->em->remove($booking);
        $this->em->flush();
    }

    public function approveRequest(BookingRequest $request, AdminUser $admin): Booking
    {
        if (BookingRequestStatus::Pending !== $request->getStatus()) {
            throw new \LogicException('Only pending requests can be approved.');
        }

        $booking = $this->createBooking(
            $request->getTrailer(),
            $request->getStartDate(),
            $request->getEndDate(),
            BookingType::Customer,
            $admin,
            null,
            $request,
            $request->getFirstName(),
            $request->getLastName(),
            $request->getEmail(),
            $request->getPhone(),
            $request->getStartTime(),
            $request->getEndTime(),
        );

        $request->setStatus(BookingRequestStatus::Approved);
        $request->setReviewedBy($admin);
        $request->setReviewedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $booking;
    }

    public function declineRequest(BookingRequest $request, AdminUser $admin, ?string $adminNote = null): void
    {
        if (BookingRequestStatus::Pending !== $request->getStatus()) {
            throw new \LogicException('Only pending requests can be declined.');
        }

        $request->setStatus(BookingRequestStatus::Declined);
        $request->setReviewedBy($admin);
        $request->setReviewedAt(new \DateTimeImmutable());
        if (null !== $adminNote) {
            $request->setAdminNote($adminNote);
        }

        $this->em->flush();
    }

    /**
     * @throws BookingOverlapException if [start, end] overlaps an existing booking for the trailer
     */
    public function assertNoOverlap(
        Trailer $trailer,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        ?Booking $excluding = null,
    ): void {
        if ($start > $end) {
            throw new \InvalidArgumentException('Start date must not be after end date.');
        }

        if ([] !== $this->bookingRepository->findOverlapping($trailer, $start, $end, $excluding)) {
            throw new BookingOverlapException('These dates overlap with an existing booking for this trailer.');
        }
    }
}
