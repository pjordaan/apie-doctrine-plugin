<?php

namespace W2w\Test\ApieDoctrinePlugin;

use Doctrine\Common\Collections\ArrayCollection;
use W2w\Lib\ApieObjectAccessNormalizer\Exceptions\ValidationException;
use W2w\Test\ApieDoctrinePlugin\Mocks\EntityWithCountry;

class ApieNormalizerPluginTest extends AbstractDoctrineTestCase
{

    /**
     * @var \W2w\Lib\Apie\Apie
     */
    private $apie;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apie = $this->createApie(
            true,
            [
                __DIR__ . '/data/createCountries.sql',
            ]
        );
    }

    public function testPersistNew()
    {
        $request = $this->createJsonRequest(
            'POST',
            '/country',
            ['arbitrary_collection' => [1, 'string'], 'country' => 2]
        );
        $result = $this->apie->getApiResourceFacade()->post(EntityWithCountry::class, $request);
        $resource = $result->getResource();
        $this->assertTrue($resource instanceof EntityWithCountry);
        $this->assertEquals('Green Hill Zone', $resource->getCountry()->getName());
        $this->assertEquals(new ArrayCollection([1, 'string']), $resource->getArbitraryCollection());
    }

    public function testPersistNew_can_not_make_new_one()
    {
        $request = $this->createJsonRequest(
            'POST',
            '/country',
            ['country' => ['name' => 'Duckburg']]
        );
        $this->expectException(ValidationException::class);
        $this->apie->getApiResourceFacade()->post(EntityWithCountry::class, $request);
    }

    public function testPersistNew_invalid_id()
    {
        $request = $this->createJsonRequest(
            'POST',
            '/country',
            ['country' => 'Germany']
        );
        $this->expectException(ValidationException::class);
        $this->apie->getApiResourceFacade()->post(EntityWithCountry::class, $request);
    }
}
