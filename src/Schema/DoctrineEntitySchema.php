<?php


namespace W2w\Lib\ApieDoctrinePlugin\Schema;


use erasys\OpenApi\Spec\v3\Schema;
use W2w\Lib\Apie\OpenApiSchema\SchemaGenerator;
use W2w\Lib\Apie\PluginInterfaces\DynamicSchemaInterface;

class DoctrineEntitySchema implements DynamicSchemaInterface
{
    private $built = [];

    public function __invoke(
        string $resourceClass, string $operation, array $groups, int $recursion, SchemaGenerator $generator
    ) {
        if ($recursion > 0 && ($operation === 'post' || $operation === 'put')) {
            if (isset($this->built[$resourceClass])) {
                return $this->built[$resourceClass];
            }
            $childSchema = $generator->createSchema($resourceClass, 'get', ['post', 'write']);
            return $this->built[$resourceClass] = new Schema([
                'type' => 'object',
                'oneOf' => [
                    $childSchema,
                    new Schema([
                        'type' => 'integer',
                    ]),
                    new Schema([
                        'type' => 'string',
                    ]),
                ]
            ]);
        }
        return null;
    }
}
