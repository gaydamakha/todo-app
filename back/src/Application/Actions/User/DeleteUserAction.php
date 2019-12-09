<?php

namespace App\Application\Actions\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $username = $request->get('username');
        //TODO: verify if it is a user himself who delete it
        $user = $this->userRepository->deleteUser($username);

        $this->logger->info("Todo of id `{$user->getId()}` was deleted.");

        return $this->respond($user->getId(), Response::HTTP_OK);
    }
}