<?php

namespace App\Application;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteVehicleUseCase
{

    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(int $id)
    {
        try {
            /** @var Vehicle $vehicle */
            $vehicle = $this->vehicleRepository->find($id);
            if (is_null($vehicle)) {
                throw new Exception("Vehicle not found");
            }
            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
