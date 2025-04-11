<?php

namespace App\Application;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Exception;

class GetVehicleByIdUseCase
{

    private VehicleRepository $vehicleRepository;

    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id)
    {
        /** @var Vehicle $vehicle */
        $vehicle = $this->vehicleRepository->find($id);

        if (is_null($vehicle)) {
            throw new Exception("Vehicle not found");
        }

        return $vehicle;
    }
}
