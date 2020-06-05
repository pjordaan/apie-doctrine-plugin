<?php


namespace W2w\Test\ApieDoctrinePlugin\Mocks;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="one_to_many")
 */
class RelationOneToMany
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity=Relations::class, inversedBy="oneToMany")
     * @var Relations
     */
    private $manyToOne;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Relations
     */
    public function getManyToOne(): Relations
    {
        return $this->manyToOne;
    }

    /**
     * @param Relations $manyToOne
     */
    public function setManyToOne(Relations $manyToOne): void
    {
        $this->manyToOne = $manyToOne;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
