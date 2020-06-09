<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use W2w\Lib\Apie\Annotations\ApiResource;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

/**
 * @ORM\Entity
 * @ORM\Table(name="country")
 * @ApiResource(
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    public function __construct()
    {
        $this->getId();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        if (null === $this->id) {
            throw new RuntimeException('This country has no id');
        }
        return $this->id;
    }

    /**
     * Get the name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
