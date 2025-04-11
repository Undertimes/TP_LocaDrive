<?php

namespace App\Controller;

use App\Application\CreateBookingUseCase;
use App\Application\GetBookingByIdUseCase;
use App\Application\GetVehicleByIdUseCase;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{

    private $createBookingUseCase;
    private $getVehicleByIdUseCase;
    private $getBookingByIdUseCase;

    public function __construct(
        CreateBookingUseCase $createBookingUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase,
        GetBookingByIdUseCase $getBookingByIdUseCase
    ) {
        $this->createBookingUseCase = $createBookingUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
        $this->getBookingByIdUseCase = $getBookingByIdUseCase;
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

            $booking = $this->createBookingUseCase->execute($startDate, $endDate, $customer, $vehicle, $hasInsurance);
            return new Response($booking->serializeToXml());
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }
    }

    #[Route('/booking/{id}', name: 'get_booking_by_id', methods: ['GET'])]
    public function getBookingById(int $id): Response
    {
        try {
            $booking = $this->getBookingByIdUseCase->execute($id);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response($booking->serializeToXml());
    }
}
