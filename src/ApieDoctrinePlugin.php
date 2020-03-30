<?php


namespace W2w\Lib\ApieDoctrinePlugin;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use W2w\Lib\Apie\Interfaces\ApiResourceFactoryInterface;
use W2w\Lib\Apie\PluginInterfaces\ApiResourceFactoryProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\PropertyInfoExtractorProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\ResourceFactories\DoctrineDataLayerFactory;

class ApieDoctrinePlugin implements ApiResourceFactoryProviderInterface, PropertyInfoExtractorProviderInterface
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

    public function getApiResourceFactory(): ApiResourceFactoryInterface
    {
        return new DoctrineDataLayerFactory($this->entityManager, $this->additionalEntityManagers);
    }

    /**
     * @return DoctrineExtractor[]
     */
    private function createDoctrineExtractors(): array
    {
        $list = [
            new DoctrineExtractor($this->entityManager)
        ];
        foreach ($this->additionalEntityManagers as $entityManager) {
            $list[] = new DoctrineExtractor($entityManager);
        }
        return $list;
    }

    /**
     * {@inheritDoc}
     */
    public function getListExtractors(): array
    {
        return $this->createDoctrineExtractors();
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeExtractors(): array
    {
        return $this->createDoctrineExtractors();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescriptionExtractors(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessExtractors(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getInitializableExtractors(): array
    {
        return [];
    }
}
