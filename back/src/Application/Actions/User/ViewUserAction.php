<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $username = strtolower($this->resolveArg('username'));
        $user = $this->userRepository->findUserOfUsername($username);

        $this->logger->info("User of id `{$user->getId()}` was viewed.");

        return $this->respondWithData($user);
    }
}
