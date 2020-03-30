<?php


namespace W2w\Lib\ApieDoctrinePlugin\Schema;


use erasys\OpenApi\Spec\v3\Schema;
use W2w\Lib\Apie\OpenApiSchema\SchemaGenerator;
use W2w\Lib\Apie\PluginInterfaces\DynamicSchemaInterface;

class DoctrineEntitySchema implements DynamicSchemaInterface
{

    public function __invoke(
        string $resourceClass, string $operation, array $groups, int $recursion, SchemaGenerator $generator
    ) {
        if ($operation === 'post' || $operation === 'put') {
            return new Schema([
                'oneOf' => [
                    new Schema([
                        'type' => 'integer',
                    ]),
                    $generator->createSchema($resourceClass, $operation, $groups),
                ]
            ]);
        }
        return null;
    }
}
