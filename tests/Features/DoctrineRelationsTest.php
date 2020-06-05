<?php


namespace W2w\Test\ApieDoctrinePlugin\Features;

use W2w\Test\ApieDoctrinePlugin\AbstractDoctrineTestCase;
use W2w\Test\ApieDoctrinePlugin\Mocks\Relations;

class DoctrineRelationsTest extends AbstractDoctrineTestCase
{
    public function testOneToManyRelation()
    {
        $apie = $this->createApie(true, []);
        $request = $this->createJsonRequest(
            'POST',
            '/relations',
            [
                'one_to_many' => [
                    [
                        'name' => 'test2',
                    ],
                ],
            ]
        );
        /** @var Relations $resource */
        $resource = $apie->getApiResourceFacade()->post(
            Relations::class,
            $request
        )->getResource();
        $list = $resource->getOneToMany()->toArray();
        $this->assertCount(1, $list);
        $this->assertEquals('test2', reset($list)->getName());

        $request = $this->createJsonRequest(
            'PUT',
            '/relations/' . $resource->getId(),
            [
                'one_to_many' => [
                    [
                        'name' => 'test',
                    ],
                    [
                        'name' => 'pizza',
                    ],
                ],
            ]
        );
        $response = $apie->getApiResourceFacade()->put(
            Relations::class,
            $resource->getId(),
            $request
        );
        /** @var Relations $resource */
        $resource = $response->getResource();
        $list = $resource->getOneToMany()->toArray();
        $this->assertCount(2, $list);
        $this->assertEquals('test', reset($list)->getName());
        $data = $response->getNormalizedData();
        $expected = [
            'id' => $resource->getId(),
            'one_to_one' => null,
            'one_to_one_inverse' => null,
            'one_to_many' => [
                [
                    'id' => reset($list)->getId(),
                    'name' => 'test',
                    'many_to_one' => 'https://api-example.nl/api/v1/relations/1',
                ],
                [
                    'id' => next($list)->getId(),
                    'name' => 'pizza',
                    'many_to_one' => 'https://api-example.nl/api/v1/relations/1',
                ],
            ],
            'many_to_many' => [],
        ];

        $this->assertEquals($expected, $data);
    }

    public function testOneToOneRelation()
    {
        $apie = $this->createApie(true, []);
        $request = $this->createJsonRequest(
            'POST',
            '/relations',
            [
                'one_to_one' => [
                    [
                        'one_to_one' => null,
                    ],
                ],
            ]
        );
        /** @var Relations $resource */
        $resource = $apie->getApiResourceFacade()->post(
            Relations::class,
            $request
        )->getResource();
        $item = $resource->getOneToOne();
        $this->assertInstanceOf(Relations::class, $item);
        $this->assertEquals(null, $item->getOneToOne());
        $this->assertEquals($resource, $item->getOneToOneInverse());

        $request = $this->createJsonRequest(
            'PUT',
            '/relations/' . $resource->getId(),
            [
                'one_to_one' => [
                    [
                        'one_to_one' => [
                            'one_to_many' => [
                                [
                                    'name' => 'test'
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );
        $response = $apie->getApiResourceFacade()->put(
            Relations::class,
            $resource->getId(),
            $request
        );
        /** @var Relations $resource */
        $resource = $response->getResource();
        $item = $resource->getOneToOne();
        $this->assertInstanceOf(Relations::class, $item);
        $this->assertEquals(null, $item->getOneToOne());
        $this->assertEquals($resource, $item->getOneToOneInverse());
        $data = $response->getNormalizedData();
        $expected = [
            'id' => $resource->getId(),
            'one_to_one' => 'https://api-example.nl/api/v1/relations/' . $resource->getOneToOne()->getId(),
            'one_to_one_inverse' => null,
            'many_to_many' => [
            ],
            'one_to_many' => [],
        ];

        $this->assertEquals($expected, $data);

        $response = $apie->getApiResourceFacade()->get(
            Relations::class,
            $resource->getOneToOne()->getId(),
            null
        );
        $resource = $response->getResource();
        $data = $response->getNormalizedData();
        $expected = [
            'id' => $resource->getId(),
            'one_to_one' => null,
            'one_to_one_inverse' => 'https://api-example.nl/api/v1/relations/' . $resource->getOneToOneInverse()->getId(),
            'many_to_many' => [
            ],
            'one_to_many' => [],
        ];

        $this->assertEquals($expected, $data);
    }

    public function testManyToManyRelation()
    {
        $apie = $this->createApie(true, []);
        $request = $this->createJsonRequest(
            'POST',
            '/relations',
            [
                'many_to_many' => [
                    [
                        'name' => 'test2',
                    ],
                ],
            ]
        );
        /** @var Relations $resource */
        $resource = $apie->getApiResourceFacade()->post(
            Relations::class,
            $request
        )->getResource();
        $list = $resource->getManyToMany()->toArray();
        $this->assertCount(1, $list);
        $this->assertEquals('test2', reset($list)->getName());

        $request = $this->createJsonRequest(
            'PUT',
            '/relations/' . $resource->getId(),
            [
                'many_to_many' => [
                    [
                        'name' => 'test',
                    ],
                    [
                        'name' => 'pizza',
                    ],
                ],
            ]
        );
        $response = $apie->getApiResourceFacade()->put(
            Relations::class,
            $resource->getId(),
            $request
        );
        /** @var Relations $resource */
        $resource = $response->getResource();
        $list = $resource->getManyToMany()->toArray();
        $this->assertCount(2, $list);
        $this->assertEquals('test', reset($list)->getName());
        $data = $response->getNormalizedData();
        $expected = [
            'id' => $resource->getId(),
            'one_to_one' => null,
            'one_to_one_inverse' => null,
            'many_to_many' => [
                'https://api-example.nl/api/v1/relation_many_to_many/' . reset($list)->getId(),
                'https://api-example.nl/api/v1/relation_many_to_many/' . next($list)->getId(),
            ],
            'one_to_many' => [],
        ];

        $this->assertEquals($expected, $data);
    }
}
