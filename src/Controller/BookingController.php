<?php

namespace App\Controller;

use App\Application\CreateBookingUseCase;
use App\Application\GetVehicleByIdUseCase;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{

    private $createBookingUseCase;
    private $getVehicleByIdUseCase;

    public function __construct(
        CreateBookingUseCase $createBookingUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase
    ) {
        $this->createBookingUseCase = $createBookingUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
    }


    #[Route('/booking/create', name: 'booking_create', methods: ['POST'])]
    public function createBooking(Request $request): Response
    {

        $startDate = new DateTime($request->request->get("startDate"));
        $endDate = new DateTime($request->request->get("endDate"));
        $customer = $this->getUser();
        $vehicleId = $request->request->get('vehicle');
        $hasInsurance = $request->request->get('hasInsurance') == 'true';

        try {
            $vehicle = $this->getVehicleByIdUseCase->execute($vehicleId);

            $this->createBookingUseCase->execute($startDate, $endDate, $customer, $vehicle, $hasInsurance);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
        return new Response();
    }
}
