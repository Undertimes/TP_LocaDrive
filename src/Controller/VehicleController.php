<?php

namespace App\Controller;

use App\Application\CreateVehicleUseCase;
use App\Application\DeleteVehicleUseCase;
use App\Application\GetVehicleByIdUseCase;
use App\Application\UpdateVehicleUseCase;
use App\Entity\UserRoles;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    private $createVehicleUseCase;
    private $getVehicleByIdUseCase;
    private $deleteVehicleUseCase;
    private $updateVehicleUseCase;

    public function __construct(
        CreateVehicleUseCase $createVehicleUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase,
        DeleteVehicleUseCase $deleteVehicleUseCase,
        UpdateVehicleUseCase $updateVehicleUseCase
    ) {
        $this->createVehicleUseCase = $createVehicleUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
        $this->deleteVehicleUseCase = $deleteVehicleUseCase;
        $this->updateVehicleUseCase = $updateVehicleUseCase;
    }


    #[Route('/vehicle/create', name: 'vehicle_create', methods: ['POST'])]
    public function createVehicle(Request $request): Response
    {
        $user = $this->getUser();

        if (in_array(UserRoles::administrator->value, $user->getRoles())) {
            return new Response("Please login as administrator to create a new vehicle.");
        }

        $model = $request->request->get("model");
        $brand = $request->request->get("brand");
        $pricePerDay = $request->request->get("pricePerDay");

        try {
            $vehicle = $this->createVehicleUseCase->execute($model, $brand, $pricePerDay);
            return new Response($vehicle->serializeToXml());
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }
    }

    #[Route('/vehicle/{id}', name: 'get_vehicle_by_id', methods: ['GET'])]
    public function getVehicleById(int $id): Response
    {

        try {
            $vehicle = $this->getVehicleByIdUseCase->execute($id);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response($vehicle->serializeToXml());
    }

    #[Route('/vehicle/delete/{id}', name: 'delete_vehicle', methods: ['POST'])]
    public function deleteVehicle(int $id): Response
    {
        $user = $this->getUser();

        if (in_array(UserRoles::administrator->value, $user->getRoles())) {
            return new Response("Please login as administrator to delete a vehicle.");
        }

        try {
            $this->deleteVehicleUseCase->execute($id);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response("Vehicle " + $id + " deleted successfully");
    }

    #[Route('/vehicle/update', name: 'vehicle_update', methods: ['POST'])]
    public function updateVehicle(Request $request): Response
    {
        $user = $this->getUser();

        if (in_array(UserRoles::administrator->value, $user->getRoles())) {
            return new Response("Please login as administrator to update a vehicle.");
        }

        $id = $request->request->get("id");
        $model = $request->request->get("model");
        $brand = $request->request->get("brand");
        $pricePerDay = $request->request->get("pricePerDay");

        try {
            $vehicle = $this->updateVehicleUseCase->execute($id, $model, $brand, $pricePerDay);
            return new Response($vehicle->serializeToXml());
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }
    }
}
