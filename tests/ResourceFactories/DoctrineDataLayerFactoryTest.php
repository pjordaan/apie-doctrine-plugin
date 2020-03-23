<?php


namespace W2w\Test\ApieDoctrinePlugin\ResourceFactories;


use RuntimeException;
use W2w\Lib\ApieDoctrinePlugin\DataLayers\DoctrineDataLayer;
use W2w\Lib\ApieDoctrinePlugin\ResourceFactories\DoctrineDataLayerFactory;
use W2w\Test\ApieDoctrinePlugin\AbstractDoctrineTestCase;

class DoctrineDataLayerFactoryTest extends AbstractDoctrineTestCase
{
    public function testDefaultEntityManager()
    {
        $defaultEntityManager = $this->createEntityManager();
        $otherEntityManager = $this->createEntityManager();
        $testItem = new DoctrineDataLayerFactory($defaultEntityManager, ['test' => $otherEntityManager]);
        $this->assertTrue($testItem->hasApiResourceRetrieverInstance(DoctrineDataLayer::class));
        $this->assertEquals(new DoctrineDataLayer($defaultEntityManager), $testItem->getApiResourceRetrieverInstance(DoctrineDataLayer::class));
        $this->assertTrue($testItem->hasApiResourcePersisterInstance(DoctrineDataLayer::class));
        $this->assertEquals(new DoctrineDataLayer($defaultEntityManager), $testItem->getApiResourcePersisterInstance(DoctrineDataLayer::class));
    }

    public function testOtherEntityManager()
    {
        $defaultEntityManager = $this->createEntityManager();
        $otherEntityManager = $this->createEntityManager();
        $testItem = new DoctrineDataLayerFactory($defaultEntityManager, ['test' => $otherEntityManager]);
        $this->assertTrue($testItem->hasApiResourceRetrieverInstance('test'));
        $this->assertEquals(new DoctrineDataLayer($otherEntityManager), $testItem->getApiResourceRetrieverInstance('test'));
        $this->assertTrue($testItem->hasApiResourcePersisterInstance('test'));
        $this->assertEquals(new DoctrineDataLayer($otherEntityManager), $testItem->getApiResourcePersisterInstance('test'));
    }

    /**
     * @dataProvider missingEntityManagerProvider
     */
    public function testMissingEntityManager(string $hasMethod, string $getMethod)
    {
        $defaultEntityManager = $this->createEntityManager();
        $otherEntityManager = $this->createEntityManager();
        $testItem = new DoctrineDataLayerFactory($defaultEntityManager, ['test' => $otherEntityManager]);
        $this->assertFalse($testItem->$hasMethod('undefined'));
        $this->expectException(RuntimeException::class);
        $testItem->$getMethod('undefined');
    }

    public function missingEntityManagerProvider()
    {
        yield ['hasApiResourceRetrieverInstance', 'getApiResourceRetrieverInstance'];
        yield ['hasApiResourcePersisterInstance', 'getApiResourcePersisterInstance'];
    }
}
