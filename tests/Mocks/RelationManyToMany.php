<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use W2w\Lib\Apie\Annotations\ApiResource;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

/**
 * @ORM\Entity
 * @ORM\Table(name="many_to_many")
 * @ApiResource(
 *     persistClass=DoctrineDataLayer::class,
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class RelationManyToMany
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity=Relations::class, inversedBy="manyToMany")
     * @var Collection
     */
    private $manyToMany;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->manyToMany = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
    public function getManyToMany(): Collection
    {
        return $this->manyToMany;
    }

    /**
     * @param Collection $manyToMany
     */
    public function setManyToMany(Collection $manyToMany): void
    {
        $this->manyToMany = $manyToMany;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
