<?php


namespace W2w\Test\ApieDoctrinePlugin;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use W2w\Lib\Apie\Apie;
use W2w\Lib\Apie\DefaultApie;
use W2w\Lib\Apie\Plugins\Core\Normalizers\ApieObjectNormalizer;
use W2w\Lib\Apie\Plugins\Core\Normalizers\ContextualNormalizer;
use W2w\Lib\Apie\Plugins\StaticConfig\StaticConfigPlugin;
use W2w\Lib\Apie\Plugins\StaticConfig\StaticResourcesPlugin;
use W2w\Lib\ApieDoctrinePlugin\ApieDoctrinePlugin;
use W2w\Test\ApieDoctrinePlugin\Mocks\Example;
use W2w\Test\ApieDoctrinePlugin\Mocks\RelationManyToMany;
use W2w\Test\ApieDoctrinePlugin\Mocks\Relations;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

abstract class AbstractDoctrineTestCase extends TestCase
{
    protected function createEntityManager(?string $path = null): EntityManagerInterface
    {
        AnnotationRegistry::registerLoader('class_exists');
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . '/Mocks'],
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
        ContextualNormalizer::disableNormalizer(ApieObjectNormalizer::class);
        ContextualNormalizer::disableDenormalizer(ApieObjectNormalizer::class);
        $em = $this->createEntityManager();
        if ($runMigrations) {
            $tool = new SchemaTool($em);
            $classes = $em->getMetadataFactory()->getAllMetadata();
            $sql = $tool->getDropDatabaseSQL($classes);
            foreach ($sql as $statement) {
                $em->getConnection()->exec($statement);
            }
            $sql = $tool->getUpdateSchemaSql($classes);
            foreach ($sql as $statement) {
                $em->getConnection()->exec($statement);
            }
            foreach ($additionalMigrations as $additionalMigration) {
                $em->getConnection()->exec(file_get_contents($additionalMigration));
            }
        }
        return DefaultApie::createDefaultApie(
            true,
            [
                new StaticResourcesPlugin([Example::class, Relations::class, RelationManyToMany::class]),
                new StaticConfigPlugin('https://api-example.nl/api/v1'),
                new ApieDoctrinePlugin($em),
            ]
        );
    }

    protected function createJsonRequest(string $method, string $uri, $postData): ServerRequestInterface
    {
        return $this->createServerRequest($method, $uri, json_encode($postData))
            ->withHeader('Content-Type', 'application/json');
    }

    protected function createServerRequest(string $method, string $uri, ?string $body = null): ServerRequestInterface
    {
        $result = (new ServerRequestFactory())
            ->createServerRequest($method, $uri, [])
            ->withHeader('Accept', 'application/json');
        if (null !== $body) {
            return $result->withBody(new Stream('data://text/plain,' . $body));
        }
        return $result;
    }
}
