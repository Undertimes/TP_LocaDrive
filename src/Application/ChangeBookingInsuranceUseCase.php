<?php

namespace App\Application;

use App\Entity\Booking;
use App\Entity\PaymentMethods;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ChangeBookingInsuranceUseCase
{

    private BookingRepository $bookingRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(BookingRepository $bookingRepository, EntityManagerInterface $entityManager)
    {
        $this->bookingRepository = $bookingRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, bool $hasInsurance)
    {
        try {
            /** @var Booking $booking */
            $booking = $this->bookingRepository->find($id);
            if (is_null($booking)) {
                throw new Exception("Booking not found");
            }

            $booking->setHasInsurance($hasInsurance);
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $booking;
    }
}
