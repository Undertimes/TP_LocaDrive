<?php

namespace App\Controller;

use ApiPlatform\GraphQl\Serializer\ObjectNormalizer;
use ApiPlatform\Serializer\JsonEncoder;
use App\Application\CreateVehicleUseCase;
use App\Application\GetVehicleByIdUseCase;
use App\Entity\UserRoles;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class VehicleController extends AbstractController
{
    private $createVehicleUseCase;
    private $getVehicleByIdUseCase;

    public function __construct(
        CreateVehicleUseCase $createVehicleUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase
    ) {
        $this->createVehicleUseCase = $createVehicleUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
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
            $this->createVehicleUseCase->execute($model, $brand, $pricePerDay);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        $encoder = new XmlEncoder();

        return new Response($encoder->encode(['model' => (string)$model, 'brand' => (string)$brand, 'pricePerDay' => (string)$pricePerDay], 'xml'));
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
}
