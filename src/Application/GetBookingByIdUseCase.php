<?php

namespace App\Application;

use App\Repository\BookingRepository;

class GetBookingByIdUseCase
{

    private BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function execute(int $id)
    {
        $booking = $this->bookingRepository->find($id);

        if (is_null($booking)) {
            throw new \Exception("Booking not found");
        }

        return $booking;
    }
}
