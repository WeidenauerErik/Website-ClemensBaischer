<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BookingRequestInput
{
    #[Assert\NotBlank(message: 'Bitte einen Anhänger auswählen.')]
    public string $trailerSlug = '';

    #[Assert\NotBlank(message: 'Bitte Vornamen angeben.')]
    #[Assert\Length(max: 100)]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Bitte Nachnamen angeben.')]
    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotBlank(message: 'Bitte E-Mail-Adresse angeben.')]
    #[Assert\Email(message: 'Bitte eine gültige E-Mail-Adresse angeben.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'Bitte Telefonnummer angeben.')]
    #[Assert\Length(max: 40)]
    public string $phone = '';

    #[Assert\Length(max: 2000)]
    public ?string $message = null;

    #[Assert\NotBlank(message: 'Bitte Startdatum angeben.')]
    #[Assert\Date]
    public string $startDate = '';

    #[Assert\NotBlank(message: 'Bitte Enddatum angeben.')]
    #[Assert\Date]
    public string $endDate = '';

    #[Assert\Regex(pattern: '/^([01]\d|2[0-3]):[0-5]\d$/', message: 'Bitte eine gültige Uhrzeit angeben (HH:MM).')]
    public ?string $startTime = null;

    #[Assert\Regex(pattern: '/^([01]\d|2[0-3]):[0-5]\d$/', message: 'Bitte eine gültige Uhrzeit angeben (HH:MM).')]
    public ?string $endTime = null;

    /**
     * Honeypot field: must stay empty. Real visitors never see or fill it;
     * bots that auto-fill every input will, letting us silently drop the submission.
     */
    public ?string $website = null;
}
