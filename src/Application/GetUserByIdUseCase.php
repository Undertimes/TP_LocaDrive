<?php

namespace App\Application;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;

class GetUserByIdUseCase
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id)
    {
        try {
            /** @var User $user */
            $user = $this->userRepository->find($id);
            if (is_null($user)) {
                throw new Exception("User not found");
            }
            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
