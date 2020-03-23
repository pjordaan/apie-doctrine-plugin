<?php


namespace W2w\Test\ApieDoctrinePlugin;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use W2w\Lib\Apie\Apie;
use W2w\Lib\Apie\DefaultApie;
use W2w\Lib\Apie\Plugins\StaticConfig\StaticResourcesPlugin;
use W2w\Lib\ApieDoctrinePlugin\ApieDoctrinePlugin;
use W2w\Test\ApieDoctrinePlugin\Mocks\Example;
use Zend\Diactoros\ServerRequestFactory;

abstract class AbstractDoctrineTestCase extends TestCase
{
    protected function createEntityManager(?string $path = null): EntityManagerInterface
    {
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__, __DIR__ . '/../src/'],
            $isDevMode,
            $proxyDir,
            $cache,
            $useSimpleAnnotationReader
        );
        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => is_null($path),
            'path'   => $path
        );

        return EntityManager::create($conn, $config);
    }

    protected function createApie(bool $runMigrations = true, array $additionalMigrations = []): Apie
    {
        $em = $this->createEntityManager();
        if ($runMigrations) {
            $em->getConnection()->exec(file_get_contents(__DIR__ . '/data/migration.sql'));
            foreach ($additionalMigrations as $additionalMigration) {
                $em->getConnection()->exec(file_get_contents($additionalMigration));
            }
        }
        return DefaultApie::createDefaultApie(
            true,
            [
                new StaticResourcesPlugin([Example::class]),
                new ApieDoctrinePlugin($em)
            ]
        );
    }

    protected function createServerRequest(string $method, string $uri): ServerRequestInterface
    {
        return (new ServerRequestFactory())
            ->createServerRequest($method, $uri, [])
            ->withHeader('Accept', 'application/json');
    }
}
