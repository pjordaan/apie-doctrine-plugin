<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use W2w\Lib\Apie\Annotations\ApiResource;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity_with_country")
 * @ApiResource(
 *     persistClass=DoctrineDataLayer::class,
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class EntityWithCountry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class)
     * @var Country
     */
    protected $country;

    /**
     * @var ArrayCollection
     */
    protected $arbitraryCollection;

    public function __construct(Country $country)
    {
        $this->arbitraryCollection = new ArrayCollection();
        $country->getId();
        $this->country = $country;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the country.
     *
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * Set the country.
     *
     * @param Country $country
     *
     * @return EntityWithCountry
     */
    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the arbitrary collection.
     *
     * @return ArrayCollection
     */
    public function getArbitraryCollection(): ArrayCollection
    {
        return $this->arbitraryCollection;
    }

    /**
     * Set the arbitrary collection.
     *
     * @param ArrayCollection $arbitraryCollection
     *
     * @return EntityWithCountry
     */
    public function setArbitraryCollection(ArrayCollection $arbitraryCollection): self
    {
        $this->arbitraryCollection = $arbitraryCollection;

        return $this;
    }
}
