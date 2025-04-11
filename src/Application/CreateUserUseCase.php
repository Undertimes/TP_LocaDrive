<?php

namespace App\Application;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserUseCase
{


    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ) {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }

    public function execute(string $mail, string $password, string $firstName, string $lastName, DateTime $licenseDate)
    {
        try {
            $user = new User($mail, $password, $this->hasher, $firstName, $lastName, $licenseDate);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
