<?php


namespace W2w\Test\ApieDoctrinePlugin\Features;


use W2w\Test\ApieDoctrinePlugin\AbstractDoctrineTestCase;

class OpenApiSchemaTest extends AbstractDoctrineTestCase
{
    const SCHEMA = __DIR__ . '/../data/openapi-schema.yml';

    public function testSchemaIsGeneratedProperly()
    {
        $apie = $this->createApie(true, []);
        $generator = $apie->getOpenApiSpecGenerator();
        $schema = $generator->getOpenApiSpec()->toYaml(20, 2);
        // file_put_contents(self::SCHEMA, $schema);
        $expected = file_get_contents(self::SCHEMA);
        $this->assertEquals($expected, $schema);
    }
}
