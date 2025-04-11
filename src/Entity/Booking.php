<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BookingRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

enum PaymentMethods: string
{
    case CB = "CB";
    case PayPal = "PayPal";
}

enum BookingStates: string
{
    case cart = "CART";
    case paid = "PAID";
    case canceled = "CANCELED";
}

#[ApiResource]
#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?Vehicle $vehicle = null;

    #[ORM\Column]
    private ?bool $hasInsurance = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentMethod = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paymentDate = null;

    public function __construct(DateTime $startDate, DateTime $endDate, User $customer, ?Vehicle $vehicle = null, bool $hasInsurance = false)
    {
        $this->verifyDates($startDate, $endDate);
        if ($vehicle->hasBookingIntersectingDates($startDate, $endDate)) {
            throw new Exception("This vehicle is already booked at those dates.");
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->customer = $customer;
        $this->vehicle = $vehicle;
        $this->hasInsurance = $hasInsurance;
        $this->status = BookingStates::cart->value;
        $this->updatePrice();
    }

    public function serializeToXml(): string
    {
        $encoder = new XmlEncoder();

        return $encoder->encode(['startDate' => $this->startDate->format(DateTimeInterface::ATOM), 'endDate' => $this->endDate->format(DateTimeInterface::ATOM), 'customer' => $this->customer->serializeToXml(), 'vehicle' => (string)$this->vehicle->serializeToXml(), 'hasInsurance' => (string)$this->hasInsurance], 'xml');
    }

    public function verifyDates(DateTime $startDate, DateTime $endDate)
    {
        if ($startDate > $endDate) {
            throw new Exception("Start date cannot be after end date.");
        }

        if ($startDate < new DateTime()) {
            throw new Exception("Start date cannot be before today.");
        }
    }

    public function updatePrice()
    {
        if ($this->status != BookingStates::cart->value) {
            throw new Exception("Price can only be updated while the booking is in the cart.");
        }
        $newTotalPrice = 0;
        if ($this->hasInsurance()) {
            $newTotalPrice += 20;
        }
        if (!is_null($this->vehicle)) {
            $numberOfDays = $this->getBookingNumberOfDays();
            $newTotalPrice += $this->vehicle->getPricePerDay() * $numberOfDays;
        }
        $this->totalPrice = $newTotalPrice;
    }

    public function getBookingNumberOfDays(): int
    {
        $interval = date_diff($this->endDate, $this->startDate, true);
        return $interval->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        $this->updatePrice();

        return $this;
    }

    public function hasInsurance(): ?bool
    {
        return $this->hasInsurance;
    }

    public function setHasInsurance(bool $hasInsurance): static
    {
        $this->hasInsurance = $hasInsurance;

        $this->updatePrice();

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function payBooking(PaymentMethods $paymentMethod)
    {
        if (is_null($this->vehicle)) {
            throw new Exception("A booking cannot be paid without a vehicle.");
        }
        if ($this->status == BookingStates::cart->value) {
            $this->status = BookingStates::paid->value;
            $this->paymentMethod = $paymentMethod->value;
            $this->paymentDate = new DateTime('now');
        }
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }
}
