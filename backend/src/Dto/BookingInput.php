<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback([BookingInput::class, 'validate'])]
final class BookingInput
{
    #[Assert\NotBlank]
    #[Assert\Date]
    public string $startDate = '';

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $endDate = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['customer', 'blocked'])]
    public string $type = 'blocked';

    #[Assert\Length(max: 2000)]
    public ?string $note = null;

    #[Assert\Length(max: 100)]
    public ?string $customerFirstName = null;

    #[Assert\Length(max: 100)]
    public ?string $customerLastName = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    public ?string $customerEmail = null;

    #[Assert\Length(max: 40)]
    public ?string $customerPhone = null;

    public static function validate(self $input, ExecutionContextInterface $context): void
    {
        if ('customer' !== $input->type) {
            return;
        }

        if (null === $input->customerFirstName || '' === trim($input->customerFirstName)) {
            $context->buildViolation('Bitte Vorname der Kundin/des Kunden angeben.')
                ->atPath('customerFirstName')
                ->addViolation();
        }

        if (null === $input->customerLastName || '' === trim($input->customerLastName)) {
            $context->buildViolation('Bitte Nachname der Kundin/des Kunden angeben.')
                ->atPath('customerLastName')
                ->addViolation();
        }

        if (null === $input->customerEmail || '' === trim($input->customerEmail)) {
            $context->buildViolation('Bitte E-Mail-Adresse der Kundin/des Kunden angeben.')
                ->atPath('customerEmail')
                ->addViolation();
        }

        if (null === $input->customerPhone || '' === trim($input->customerPhone)) {
            $context->buildViolation('Bitte Telefonnummer der Kundin/des Kunden angeben.')
                ->atPath('customerPhone')
                ->addViolation();
        }
    }
}
