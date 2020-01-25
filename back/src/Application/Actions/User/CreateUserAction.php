<?php

namespace App\Application\Actions\User;

use App\Application\Actions\PostActionInterface;
use App\Domain\DomainException\DomainInvalidValueException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserAction extends UserAction implements PostActionInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws DomainInvalidValueException
     */
    public function action(Request $request): Response
    {
        $body = $request->request->all();

        $this->validateBody($body);

        $data = $body['data'];
        $username = $data['username'];
        $password = $data['password'];
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];

        $user = $this->userRepository->createUser($username, $password, $firstname, $lastname);

        return $this->respond(["id"=>$user->getId()], Response::HTTP_CREATED);
    }

    /**
     * @param array $body
     * @return void
     */
    public function validateBody(array &$body): void
    {
        $constraints = new Assert\Collection([
            'data' => new Assert\Collection([
                'username' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ],
                'password' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8])
                ],
                'firstname' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string'])
                ],
                'lastname' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string'])
                ],
            ])
        ]);
        $violations = $this->validator->validate($body, $constraints);
        if (0 !== count($violations)) {
            throw new DomainInvalidValueException((string) $violations);
        }
    }
}