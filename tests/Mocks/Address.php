<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Address
{
    /** @ORM\Column(type = "string") */
    private $street;

    /** @ORM\Column(type = "string") */
    private $streetNumber;

    /** @ORM\Column(type = "string") */
    private $postalCode;

    /** @ORM\Column(type = "string") */
    private $city;

    public function __construct(string $street, string $streetNumber, string $postalCode, string $city)
    {
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
    }

    /**
     * Get the street.
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Get the street number.
     *
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * Get the postal code.
     *
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * Get the city.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
}
