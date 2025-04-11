<?php

namespace App\Application;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Exception;

class GetBookingByIdUseCase
{

    private BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function execute(int $id)
    {
        try {
            /** @var Booking $booking */
            $booking = $this->bookingRepository->find($id);
            if (is_null($booking)) {
                throw new Exception("Booking not found");
            }
            return $booking;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
