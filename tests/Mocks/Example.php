<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use W2w\Lib\Apie\Annotations\ApiResource;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

/**
 * @ORM\Entity
 * @ORM\Table(name="example")
 * @ApiResource(
 *     persistClass=DoctrineDataLayer::class,
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class Example
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '<not specified>';

    /**
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->createdAt = $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return Example
     */
    public function setName($name): self
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
