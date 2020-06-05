<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use W2w\Lib\Apie\Annotations\ApiResource;
use W2w\Lib\Apie\Exceptions\InvalidClassTypeException;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

/**
 * @ORM\Entity
 * @ORM\Table(name="relations")
 * @ApiResource(
 *     persistClass=DoctrineDataLayer::class,
 *     retrieveClass=DoctrineDataLayer::class
 * )
 */
class Relations
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=RelationManyToOne::class,cascade={"persist"})
     * @var RelationManyToOne
     */
    private $manyToOne;

    /**
     * @ORM\OneToOne(targetEntity=Relations::class,cascade={"persist"})
     * @var Relations|null
     */
    private $oneToOne;

    /**
     * @ORM\OneToOne(targetEntity=Relations::class, mappedBy="oneToOne", cascade={"persist"})
     * @var Relations|null
     */
    private $oneToOneInverse;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=RelationOneToMany::class, mappedBy="manyToOne", cascade={"persist"})
     */
    private $oneToMany;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=RelationManyToMany::class, mappedBy="relations", cascade={"persist"})
     */
    private $manyToMany;

    public function __construct() {
        $this->manyToOne = new RelationManyToOne();
        $this->oneToMany = new ArrayCollection();
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
     * @return Relations|null
     */
    public function getOneToOne(): ?Relations
    {
        return $this->oneToOne;
    }

    /**
     * @param Relations|null $oneToOne
     */
    public function setOneToOne(?Relations $oneToOne): void
    {
        if ($this->oneToOne) {
            $this->oneToOne->oneToOneInverse = null;
        }
        $this->oneToOne = $oneToOne;
        if ($oneToOne) {
            $this->oneToOne->oneToOneInverse = $this;
        }
    }

    /**
     * @return Relations|null
     */
    public function getOneToOneInverse(): ?Relations
    {
        return $this->oneToOneInverse;
    }

    /**
     * @param Relations|null $oneToOneInverse
     */
    public function setOneToOneInverse(?Relations $oneToOneInverse): void
    {
        $this->oneToOneInverse = $oneToOneInverse;
    }

    /**
     * @return Collection
     */
    public function getOneToMany(): Collection
    {
        return $this->oneToMany;
    }

    /**
     * @param Collection $oneToMany
     */
    public function setOneToMany(Collection $oneToMany): void
    {
        foreach ($oneToMany as $item) {
            if (!$item instanceof RelationOneToMany) {
                throw new InvalidClassTypeException(get_class($item), RelationOneToMany::class);
            }
            $item->setManyToOne($this);
        }
        $this->oneToMany = $oneToMany;
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
}
