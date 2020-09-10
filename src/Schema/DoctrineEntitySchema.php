<?php


namespace W2w\Lib\ApieDoctrinePlugin\Schema;

use erasys\OpenApi\Spec\v3\Schema;
use W2w\Lib\Apie\OpenApiSchema\OpenApiSchemaGenerator;
use W2w\Lib\Apie\OpenApiSchema\SchemaGenerator;
use W2w\Lib\Apie\PluginInterfaces\DynamicSchemaInterface;
use W2w\Lib\ApieDoctrinePlugin\ApieNormalizerPlugin;
use W2w\Lib\ApieDoctrinePlugin\Normalizers\DoctrinePrimaryKeyToEntityNormalizer;

/**
 * Schema builder for related entities to tell the OpenAPI schema that an integer or string are also possible to be
 * used in a post or put.
 *
 * @see ApieNormalizerPlugin
 * @see DoctrinePrimaryKeyToEntityNormalizer
 */
class DoctrineEntitySchema implements DynamicSchemaInterface
{
    private $built = [];

    public function __invoke(
        string $resourceClass,
        string $operation,
        array $groups,
        int $recursion,
        OpenApiSchemaGenerator $generator
    ): ?Schema {
        if ($recursion > 0 && ($operation === 'post' || $operation === 'put')) {
            if (isset($this->built[$resourceClass])) {
                return $this->built[$resourceClass];
            }
            $childSchema = $generator->createSchema($resourceClass, 'get', ['post', 'write']);
            return $this->built[$resourceClass] = new Schema([
                'type' => 'object',
                'oneOf' => [
                    new Schema([
                        'type' => 'integer',
                    ]),
                    new Schema([
                        'type' => 'string',
                    ]),
                    $childSchema,
                ]
            ]);
        }
        return null;
    }
}
