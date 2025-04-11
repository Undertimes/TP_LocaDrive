<?php

namespace App\Application;

use App\Repository\VehicleRepository;

class GetVehicleByIdUseCase
{

    private VehicleRepository $vehicleRepository;

    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id)
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (is_null($vehicle)) {
            throw new \Exception("Vehicle not found");
        }

        return $vehicle;
    }
}
