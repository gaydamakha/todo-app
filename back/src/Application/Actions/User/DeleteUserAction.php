<?php

namespace App\Application\Actions\User;

use App\Domain\DomainException\DomainForbiddenException;
use App\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $username = $request->get('username');

        if ($username !== $currentUser->getUsername()) {
            //TODO: 401 instead of 403?
            throw new DomainForbiddenException();
        }

        $user = $this->userRepository->deleteUser($username);

        $this->logger->info("Todo of id `{$user->getId()}` was deleted.");

        return $this->respond($user->getId(), Response::HTTP_OK);
    }
}