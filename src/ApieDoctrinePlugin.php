<?php


namespace W2w\Lib\ApieDoctrinePlugin;


use Doctrine\ORM\EntityManagerInterface;
use W2w\Lib\Apie\Interfaces\ApiResourceFactoryInterface;
use W2w\Lib\Apie\PluginInterfaces\ApiResourceFactoryProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\ResourceFactories\DoctrineDataLayerFactory;

class ApieDoctrinePlugin implements ApiResourceFactoryProviderInterface
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
     * ApieDoctrinePlugin constructor.
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

    public function getApiResourceFactory(): ApiResourceFactoryInterface
    {
        return new DoctrineDataLayerFactory($this->entityManager, $this->additionalEntityManagers);
    }
}
