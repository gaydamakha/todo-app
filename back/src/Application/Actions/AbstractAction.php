<?php

namespace App\Application\Actions;

use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractAction extends AbstractFOSRestController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws DomainInvalidValueException
     */
    abstract public function action(Request $request): Response;

    /**
     * @param $data
     * @return Response
     */
    protected function respond($data, $statusCode): Response
    {
        return $this->handleView(new View(['data' => $data], $statusCode));
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return void
     */
    protected function ensureKeyExists(array &$array, string $key, $default): void
    {
        $array[$key] = array_key_exists($key, $array)
            ? $array[$key]
            : $default;
    }
}
