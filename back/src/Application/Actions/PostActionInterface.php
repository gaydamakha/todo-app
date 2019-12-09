<?php


namespace App\Application\Actions;


interface PostActionInterface
{
    /**
     * @param array $body
     * @return void
     */
    public function validateBody(array &$body): void;
}
