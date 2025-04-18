<?php

namespace App\Application;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Vehicle;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateBookingUseCase
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(DateTime $startDate, DateTime $endDate, ?User $customer, ?Vehicle $vehicle = null, bool $hasInsurance = false)
    {
        try {
            if (is_null($customer)) {
                throw new Exception("Please login before creating a booking.");
            }
            $invoice = new Booking($startDate, $endDate, $customer, $vehicle, $hasInsurance);
            $this->entityManager->persist($invoice);
            $this->entityManager->flush();
            return $invoice;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
