<?php


namespace App\Application\Actions\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewUserAction extends UserAction
{
    /**
     * @return Response
     */
    public function action(Request $request): Response
    {
        $username = $request->get('username');

        $user = $this->userRepository->findUserOfUsername($username);

        $this->logger->info("User `{$user->getId()}` was viewed.");

        return $this->respond($user, Response::HTTP_OK);
    }
}
