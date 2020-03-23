<?php


namespace W2w\Test\ApieDoctrinePlugin;

use W2w\Lib\Apie\Exceptions\ResourceNotFoundException;
use W2w\Test\ApieDoctrinePlugin\Mocks\Example;
use Zend\Diactoros\Stream;

class ApieDoctrinePluginTest extends AbstractDoctrineTestCase
{
    public function testPersistNew()
    {
        $apie = $this->createApie(true);
        $request = $this->createServerRequest('POST', '/example')
            ->withBody(new Stream('data://text/plain,' . json_encode(['slug' => 'name', 'name' => 'Bean'])));
        $result = $apie->getApiResourceFacade()->post(Example::class, $request);
        $data = $result->getNormalizedData();
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals('name', $data['slug']);
        $this->assertEquals('Bean', $data['name']);
        return [$apie, $result->getResource()];
    }

    /**
     * @depends testPersistNew
     */
    public function testPersistExisting(array $result)
    {
        $apie = $result[0];
        $entity = $result[1];
        $originalId = $entity->getId();
        $originalCreatedAt = $entity->getCreatedAt();
        $originalUpdatedAt = $entity->getUpdatedAt();
        usleep(100);
        $request = $this->createServerRequest('PUT', '/example/' . $entity->getId())
            ->withBody(new Stream('data://text/plain,' . json_encode(['slug' => 'ignored', 'name' => 'Mr. Bean'])));
        $result = $apie->getApiResourceFacade()->put(Example::class, $entity->getId(), $request);
        $newEntity = $result->getResource();
        $this->assertEquals($originalId, $newEntity->getId());
        $this->assertEquals($originalCreatedAt, $newEntity->getCreatedAt());
        $this->assertGreaterThan($originalUpdatedAt, $newEntity->getUpdatedAt());
        $this->assertEquals('Mr. Bean', $newEntity->getName());
        $this->assertEquals('name', $newEntity->getSlug());
        return [$apie, $result->getResource()];
    }

    /**
     * @depends testPersistExisting
     */
    public function testRemove(array $result)
    {
        $apie = $result[0];
        $entity = $result[1];
        $originalId = $entity->getId();
        $request = $this->createServerRequest('DELETE', '/example/' . $originalId);
        $result = $apie->getApiResourceFacade()->delete(Example::class, $entity->getId(), $request);
        $this->assertNull($result->getResource());

        $this->expectException(ResourceNotFoundException::class);
        $apie->getApiResourceFacade()->get(Example::class, $originalId, null);
    }

    public function testRetrieveAll()
    {
        $apie = $this->createApie(true, [__DIR__ . '/data/filldata.sql']);
        $request = $this->createServerRequest('GET', '/example');
        $result = $apie->getApiResourceFacade()->getAll(Example::class, $request);
        $data = $result->getNormalizedData();
        $expected = [
            [
                'id' => 1,
                'slug' => 'Poison Slug',
                'name' => 'Commander Keen 4',
                'created_at' => '1991-12-15 15:52:01',
                'updated_at' => '2005-08-15 15:52:01',
            ],
            [
                'id' => 2,
                'slug' => 'Dr. Proton',
                'name' => 'Duke Nukem: Shrapnel City',
                'created_at' => '1990-06-01 10:51:01',
                'updated_at' => '2005-08-15 15:52:01',
            ]
        ];
        $this->assertEquals($expected, $data);
    }
}
