<?php


namespace W2w\Lib\ApieDoctrinePlugin;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use erasys\OpenApi\Spec\v3\Schema;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use W2w\Lib\Apie\Interfaces\ApiResourceFactoryInterface;
use W2w\Lib\Apie\PluginInterfaces\ApiResourceFactoryProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\NormalizerProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\ObjectAccessProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\PropertyInfoExtractorProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\SchemaProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\Normalizers\CollectionNormalizer;
use W2w\Lib\ApieDoctrinePlugin\ObjectAccess\DoctrineEntityObjectAccess;
use W2w\Lib\ApieDoctrinePlugin\ResourceFactories\DoctrineDataLayerFactory;

class ApieDoctrinePlugin implements ApiResourceFactoryProviderInterface, NormalizerProviderInterface, PropertyInfoExtractorProviderInterface, ObjectAccessProviderInterface, SchemaProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var ObjectManager[]
     */
    private $additionalEntityManagers;

    /**
     * @param ObjectManager $entityManager
     * @param ObjectManager[] $additionalEntityManagers
     */
    public function __construct(
        ObjectManager $entityManager,
        array $additionalEntityManagers = []
    ) {
        $this->entityManager = $entityManager;
        $this->additionalEntityManagers = $additionalEntityManagers;
    }

    /**
     * Creates a ApieDoctrinePlugin from an entity manager registry.
     *
     * @param ManagerRegistry $registry
     * @return ApieDoctrinePlugin
     */
    public static function createFromRegistry(ManagerRegistry $registry)
    {
        $defaultName = $registry->getDefaultManagerName();
        $defaultManager = $registry->getManager($defaultName);
        $otherManagers = [];
        foreach ($registry->getManagers() as $name => $manager) {
            $otherManagers[$name] = $manager;
        }
        return new self($defaultManager, $otherManagers);
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
        $list = [];
        if ($this->entityManager instanceof EntityManagerInterface) {
            $list[] = new DoctrineExtractor($this->entityManager);
        }
        foreach ($this->additionalEntityManagers as $entityManager) {
            if ($entityManager instanceof EntityManagerInterface) {
                $list[] = new DoctrineExtractor($entityManager);
            }
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

    /**
     * {@inheritDoc}
     */
    public function getObjectAccesses(): array
    {
        $list = [];
        $this->mergeObjectAccess($list, $this->entityManager);
        foreach ($this->additionalEntityManagers as $additionalEntityManager) {
            $this->mergeObjectAccess($list, $additionalEntityManager);
        }
        return $list;
    }

    private function mergeObjectAccess(array &$array, ObjectManager $objectManager)
    {
        $metas = $objectManager->getMetadataFactory()->getAllMetadata();
        $objectAccess = new DoctrineEntityObjectAccess($objectManager, true, false);
        foreach ($metas as $meta) {
            $array[$meta->getName()] = $objectAccess;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizers(): array
    {
        $list = [new CollectionNormalizer($this->entityManager)];
        foreach ($this->additionalEntityManagers as $additionalEntityManager) {
            $list[] = new CollectionNormalizer($additionalEntityManager);
        }
        return $list;
    }

    /**
     * @return Schema[]
     */
    public function getDefinedStaticData(): array
    {
        return [];
    }

    /**
     * @return callable[]
     */
    public function getDynamicSchemaLogic(): array
    {
        return [];
    }
}
