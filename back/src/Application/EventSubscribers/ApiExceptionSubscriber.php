<?php

namespace App\Application\EventSubscribers;

use App\Domain\DomainException\DomainException;
use App\Domain\DomainException\DomainForbiddenException;
use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        /** @var Throwable $exception */
        $exception = $event->getThrowable();
        if (!$exception instanceof DomainException) {
            return;
        }

        $message = $exception->getMessage();
        $status = Response::HTTP_BAD_REQUEST;

        if ($exception instanceof DomainInvalidValueException) {
            $status = Response::HTTP_BAD_REQUEST;
        } elseif ($exception instanceof  DomainRecordNotFoundException) {
            $status = Response::HTTP_NOT_FOUND;
        } elseif ($exception instanceof DomainForbiddenException){
            $status = Response::HTTP_FORBIDDEN;
        }

        $response = new JsonResponse(
            ["error" => $message], $status, ['Content-Type'=>'application/json']
        );

        $event->setResponse($response);
    }
}
