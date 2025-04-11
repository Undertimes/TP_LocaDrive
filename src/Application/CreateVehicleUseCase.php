<?php

namespace App\Application;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
            return $vehicle;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
