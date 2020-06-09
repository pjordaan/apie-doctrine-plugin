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
 * @ORM\Table(name="entity_with_embeddable")
 * @ApiResource(
 *     persistClass=DoctrineDataLayer::class,
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class EntityWithEmbeddable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Embedded(class = Address::class) */
    public $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Get the id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
