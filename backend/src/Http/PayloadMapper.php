<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Deserializes a JSON request body into a DTO and validates it.
 *
 * Deliberately explicit rather than relying on #[MapRequestPayload]: in this
 * project's Symfony version, a plain DTO class under the autowired src/
 * resource gets grabbed by ServiceValueResolver as a fresh, unvalidated,
 * empty instance before the request-payload resolver ever runs (verified via
 * `bin/console debug:container --tag=controller.argument_value_resolver`,
 * which shows RequestPayloadValueResolver missing from the classic resolver
 * chain entirely). This does the same job without depending on that wiring.
 */
final class PayloadMapper
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function map(Request $request, string $type): object
    {
        try {
            $payload = $this->serializer->deserialize($request->getContent() ?: '{}', $type, 'json');
        } catch (SerializerExceptionInterface $e) {
            throw new BadRequestHttpException('Invalid JSON payload.', $e);
        }

        $violations = $this->validator->validate($payload);
        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $path = $violation->getPropertyPath();
                $messages[] = ('' !== $path ? $path.': ' : '').$violation->getMessage();
            }

            throw new UnprocessableEntityHttpException(implode(' | ', $messages));
        }

        return $payload;
    }
}
