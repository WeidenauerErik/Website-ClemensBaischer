<?php

namespace App\Entity;

use App\Enum\BookingType;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Trailer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trailer $trailer = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(length: 20, enumType: BookingType::class)]
    private BookingType $type;

    #[ORM\OneToOne(targetEntity: BookingRequest::class, inversedBy: 'booking')]
    #[ORM\JoinColumn(nullable: true, unique: true, onDelete: 'SET NULL')]
    private ?BookingRequest $sourceRequest = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $customerFirstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $customerLastName = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $customerEmail = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $customerPhone = null;

    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?AdminUser $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Trailer $trailer,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        BookingType $type,
        AdminUser $createdBy,
    ) {
        $this->trailer = $trailer;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
        $this->createdBy = $createdBy;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrailer(): ?Trailer
    {
        return $this->trailer;
    }

    public function setTrailer(Trailer $trailer): static
    {
        $this->trailer = $trailer;

        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;
        $this->touch();

        return $this;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        $this->touch();

        return $this;
    }

    public function getType(): BookingType
    {
        return $this->type;
    }

    public function setType(BookingType $type): static
    {
        $this->type = $type;
        $this->touch();

        return $this;
    }

    public function getSourceRequest(): ?BookingRequest
    {
        return $this->sourceRequest;
    }

    public function setSourceRequest(?BookingRequest $sourceRequest): static
    {
        $this->sourceRequest = $sourceRequest;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;
        $this->touch();

        return $this;
    }

    public function getCustomerFirstName(): ?string
    {
        return $this->customerFirstName;
    }

    public function setCustomerFirstName(?string $customerFirstName): static
    {
        $this->customerFirstName = $customerFirstName;
        $this->touch();

        return $this;
    }

    public function getCustomerLastName(): ?string
    {
        return $this->customerLastName;
    }

    public function setCustomerLastName(?string $customerLastName): static
    {
        $this->customerLastName = $customerLastName;
        $this->touch();

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;
        $this->touch();

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(?string $customerPhone): static
    {
        $this->customerPhone = $customerPhone;
        $this->touch();

        return $this;
    }

    public function getCreatedBy(): ?AdminUser
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
