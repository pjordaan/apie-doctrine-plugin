<?php


namespace W2w\Lib\ApieDoctrinePlugin;

use Doctrine\Persistence\ObjectManager;
use erasys\OpenApi\Spec\v3\Schema;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use W2w\Lib\Apie\PluginInterfaces\NormalizerProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\SchemaProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\Normalizers\DoctrineEntityNormalizer;
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
     * @return NormalizerInterface[]|DenormalizerInterface[]
     */
    public function getNormalizers(): array
    {
        $result = [];
        foreach ($this->classMapping as $className => $normalizer) {
            if ($normalizer instanceof DenormalizerInterface) {
                $result[] = $normalizer;
            } else if (is_a($normalizer, DoctrineEntityNormalizer::class, true)) {
                $result[] = new $normalizer($className, $this->objectManager);
            }
        }
        return $result;
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
        return array_map(
            function () {
                return new DoctrineEntitySchema();
            },
            $this->classMapping
        );
    }
}
