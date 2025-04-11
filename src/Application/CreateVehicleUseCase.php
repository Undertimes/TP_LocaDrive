<?php

namespace App\Application;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class CreateVehicleUseCase
{


    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function execute(string $model, string $brand, float $pricePerDay)
    {
        try {
            $vehicle = new Vehicle($model, $brand, $pricePerDay);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new Exception('Cannot create vehicle.');
        }

        return $vehicle;
    }
}
