<?php


namespace App\Application\Actions\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListUsersAction extends UserAction
{
    /**
     * @return Response
     */
    public function action(Request $request): Response
    {
        $users = $this->userRepository->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respond($users, Response::HTTP_OK);
    }
}
