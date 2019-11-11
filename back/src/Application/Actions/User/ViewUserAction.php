<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use MongoDB\BSON\ObjectId;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = new ObjectId($this->resolveArg('id'));
        $user = $this->userRepository->findUserOfId($userId);

        $this->logger->info("User of id `${userId}` was viewed.");

        return $this->respondWithData($user);
    }
}
