<?php

namespace App\Controller;

use App\Application\ChangeBookingInsuranceUseCase;
use App\Application\CreateBookingUseCase;
use App\Application\GetBookingByIdUseCase;
use App\Application\GetVehicleByIdUseCase;
use App\Application\PayBookingUseCase;
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
    private $payBookingUseCase;
    private $changeBookingInsuranceUseCase;

    public function __construct(
        CreateBookingUseCase $createBookingUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase,
        GetBookingByIdUseCase $getBookingByIdUseCase,
        PayBookingUseCase $payBookingUseCase,
        ChangeBookingInsuranceUseCase $changeBookingInsuranceUseCase
    ) {
        $this->createBookingUseCase = $createBookingUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
        $this->getBookingByIdUseCase = $getBookingByIdUseCase;
        $this->payBookingUseCase = $payBookingUseCase;
        $this->changeBookingInsuranceUseCase = $changeBookingInsuranceUseCase;
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

    #[Route('/booking/pay', name: 'pay_booking', methods: ['POST'])]
    public function payBooking(Request $request): Response
    {
        $id = $request->request->get('id');
        $paymentMethod = $request->request->get('paymentMethod');

        try {
            $booking = $this->payBookingUseCase->execute($id, $paymentMethod);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response($booking->serializeToXml());
    }

    #[Route('/booking/changeInsurance', name: 'change_booking_insurance', methods: ['POST'])]
    public function changeInsurance(Request $request): Response
    {
        $id = $request->request->get('id');
        $hasInsurance = $request->request->get('hasInsurance') == 'true';

        try {
            $booking = $this->changeBookingInsuranceUseCase->execute($id, $hasInsurance);
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return new Response($booking->serializeToXml());
    }
}
