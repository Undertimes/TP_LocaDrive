<?php

namespace App\Application;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateVehicleUseCase
{
    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, string $model, string $brand, float $pricePerDay)
    {
        try {
            /** @var Vehicle $vehicle */
            $vehicle = $this->vehicleRepository->find($id);
            $vehicle->update($model, $brand, $pricePerDay);
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
            return $vehicle;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
