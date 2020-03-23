<?php


namespace W2w\Lib\ApieDoctrinePlugin\ResourceFactories;


use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use W2w\Lib\Apie\Interfaces\ApiResourceFactoryInterface;
use W2w\Lib\Apie\Interfaces\ApiResourcePersisterInterface;
use W2w\Lib\Apie\Interfaces\ApiResourceRetrieverInterface;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;

class DoctrineDataLayerFactory implements ApiResourceFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityManagerInterface[]
     */
    private $additionalEntityManagers;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EntityManagerInterface[] $additionalEntityManagers
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        array $additionalEntityManagers = []
    ) {
        $this->entityManager = $entityManager;
        $this->additionalEntityManagers = $additionalEntityManagers;
    }

    /**
     * {@inheritDoc}
     */
    public function hasApiResourceRetrieverInstance(string $identifier): bool
    {
        if ($identifier === DoctrineDataLayer::class) {
            return true;
        }
        return isset($this->additionalEntityManagers[$identifier]);
    }

    /**
     * {@inheritDoc}
     */
    public function getApiResourceRetrieverInstance(string $identifier): ApiResourceRetrieverInterface
    {
        if ($identifier === DoctrineDataLayer::class) {
            return new DoctrineDataLayer($this->entityManager);
        }
        if (!isset($this->additionalEntityManagers[$identifier])) {
            throw new RuntimeException($identifier . ' is not registered as an entity manager');
        }
        return new DoctrineDataLayer($this->additionalEntityManagers[$identifier]);
    }

    /**
     * {@inheritDoc}
     */
    public function hasApiResourcePersisterInstance(string $identifier): bool
    {
        return $this->hasApiResourceRetrieverInstance($identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function getApiResourcePersisterInstance(string $identifier): ApiResourcePersisterInterface
    {
        return $this->getApiResourceRetrieverInstance($identifier);
    }
}
