<?php

namespace W2w\Lib\ApieDoctrinePlugin;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use W2w\Lib\Apie\PluginInterfaces\NormalizerProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\SchemaProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\Normalizers\DoctrinePrimaryKeyToEntityNormalizer;
use W2w\Lib\ApieDoctrinePlugin\Schema\DoctrineEntitySchema;

class ApieNormalizerPlugin implements NormalizerProviderInterface, SchemaProviderInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array
     */
    private $classMapping;

    public function __construct(ObjectManager $objectManager, array $classMapping)
    {
        $this->objectManager = $objectManager;
        $this->classMapping = $classMapping;
    }

    /**
     * {@inheritDoc}
     */
    public function getNormalizers(): array
    {
        $result = [];
        foreach ($this->classMapping as $className => $normalizer) {
            if ($normalizer instanceof DenormalizerInterface) {
                $result[] = $normalizer;
            } else if (is_a($normalizer, DoctrinePrimaryKeyToEntityNormalizer::class, true)) {
                $result[] = new $normalizer($className, $this->objectManager);
            }
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinedStaticData(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getDynamicSchemaLogic(): array
    {
        return array_map(
            function () {
                return new DoctrineEntitySchema();
            },
            $this->classMapping
        );
    }
}
