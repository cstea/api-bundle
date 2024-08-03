<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExceptionResponse
 *
 * @package Cstea\ApiBundle\Exception
 */
class ExceptionResponse
{
    /**
     * Generates a proper HTTP response for the provided error message.
     *
     * @param string  $message Response message.
     * @param int     $code    Response code.
     * @param mixed[] $errors  Validation errors.
     * @return JsonResponse
     */
    private function createResponse(string $message, int $code, array $errors = []): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $code,
                'message' => $message,
                'field_errors' => $errors,
            ],
            $code
        );
    }

    /**
     * Returns an HTTP response to return when an Exception is thrown.
     *
     * @param \Throwable $exception Exception to capture.
     * @return Response|null
     */
    public function __invoke(\Throwable $exception): ?Response
    {
        $response = null;

        if ($exception instanceof \Cstea\ApiBundle\Exception\RecordNotFoundException) {
            $response = $this->createResponse('Resource not found', Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof \InvalidArgumentException) {
            $response = $this->createResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof \Cstea\ApiBundle\Exception\RecordLookupException
            || $exception instanceof \Cstea\ApiBundle\Exception\RecordPersistException
        ) {
            $response = $this->createResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof \Cstea\ApiBundle\Exception\UserUnauthorizedException) {
            $response = $this->createResponse('Unauthorized', Response::HTTP_UNAUTHORIZED, $exception->getErrors());
        }

        if ($exception instanceof \Cstea\ApiBundle\Exception\UserForbiddenException) {
            $response = $this->createResponse('Forbidden', Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $response = $this->createResponse($exception->getMessage(), $exception->getStatusCode());
        }
        
        if ($exception instanceof \Cstea\ApiBundle\Exception\RecordValidationException) {
            $errors = $exception->getErrors();
            $outputErrors = [];
            
            for ($i = 0; $i < $errors->count(); $i += 1) {
                $error = $errors->get($i);
                $property = \strtolower( // Convert field name from camelCase to snake_case
                    preg_replace(
                        '/(?<!^)[A-Z]/',
                        '_$0',
                        $error->getPropertyPath()
                    )
                );
                $outputErrors[$property][] = $error->getMessage();
            }
            
            $response = $this->createResponse(
                'Validation Failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $outputErrors
            );
        }
        
        return $response;
    }
}
