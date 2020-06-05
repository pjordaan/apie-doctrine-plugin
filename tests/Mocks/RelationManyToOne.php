<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="many_to_one")
 */
class RelationManyToOne
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Relations::class, mappedBy="manyToOne")
     */
    private $relations;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }
}
