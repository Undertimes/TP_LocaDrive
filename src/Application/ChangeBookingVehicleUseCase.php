<?php

namespace App\Application;

use App\Entity\Booking;
use App\Entity\Vehicle;
use App\Repository\BookingRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ChangeBookingVehicleUseCase
{

    private BookingRepository $bookingRepository;
    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BookingRepository $bookingRepository,
        EntityManagerInterface $entityManager,
        VehicleRepository $vehicleRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->entityManager = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $bookingId, int $vehicleId)
    {
        try {
            /** @var Booking $booking */
            $booking = $this->bookingRepository->find($bookingId);
            if (is_null($booking)) {
                throw new Exception("Booking not found");
            }

            /** @var Vehicle $vehicle */
            $vehicle = $this->vehicleRepository->find($vehicleId);
            if (is_null($vehicle)) {
                throw new Exception("Vehicle not found");
            }

            $booking->setVehicle($vehicle);
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $booking;
    }
}
