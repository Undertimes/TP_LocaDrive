<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VehicleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

#[ApiResource]
#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?float $pricePerDay = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'vehicle')]
    private Collection $bookings;

    public function __construct(string $model, string $brand, float $pricePerDay)
    {
        $this->model = $model;
        $this->brand = $brand;
        $this->pricePerDay = $pricePerDay;
        $this->bookings = new ArrayCollection();
    }

    public function serializeToXml(): string
    {
        $encoder = new XmlEncoder();

        return $encoder->encode(['model' => $this->model, 'brand' => $this->brand, 'pricePerDay' => (string)$this->pricePerDay], 'xml');
    }

    public function update(string $model, string $brand, float $pricePerDay)
    {
        $this->model = $model;
        $this->brand = $brand;
        $this->pricePerDay = $pricePerDay;
    }

    public function hasBookingIntersectingDates(DateTime $startDate, DateTime $endDate): bool
    {
        $filteredCollection = $this->bookings->filter(function ($item) use ($startDate, $endDate) {
            if (($item->getStartDate() < $endDate) && ($item->getEndDate() > $startDate)) {
                return true;
            }
            return false;
        });
        return $filteredCollection->count() > 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPricePerDay(): ?float
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(float $pricePerDay): static
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setVehicle($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getVehicle() === $this) {
                $booking->setVehicle(null);
            }
        }

        return $this;
    }
}
