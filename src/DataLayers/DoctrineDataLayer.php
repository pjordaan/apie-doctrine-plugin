<?php


namespace W2w\Lib\ApieDoctrinePlugin\DataLayers;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\Persistence\ObjectManager;
use W2w\Lib\Apie\Core\SearchFilters\SearchFilterFromMetadataTrait;
use W2w\Lib\Apie\Core\SearchFilters\SearchFilterRequest;
use W2w\Lib\Apie\Exceptions\ResourceNotFoundException;
use W2w\Lib\Apie\Interfaces\ApiResourcePersisterInterface;
use W2w\Lib\Apie\Interfaces\ApiResourceRetrieverInterface;
use W2w\Lib\Apie\Interfaces\SearchFilterProviderInterface;
use W2w\Lib\ApieDoctrinePlugin\Exceptions\RemoveConflictException;

class DoctrineDataLayer implements ApiResourceRetrieverInterface, ApiResourcePersisterInterface, SearchFilterProviderInterface
{
    use SearchFilterFromMetadataTrait;

    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Persist a new API resource. Should return the new API resource.
     *
     * @param mixed $resource
     * @param array $context
     * @return mixed
     */
    public function persistNew($resource, array $context = [])
    {
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
        return $resource;
    }

    /**
     * Persist an existing API resource. The input resource is the modified API resource. Should return the new API
     * resource.
     *
     * @param mixed $resource
     * @param string|int $int
     * @param array $context
     * @return mixed
     */
    public function persistExisting($resource, $int, array $context = [])
    {
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
        return $resource;
    }

    /**
     * Removes an existing API resource.
     *
     * @param string $resourceClass
     * @param string|int $id
     * @param array $context
     * @return mixed
     */
    public function remove(string $resourceClass, $id, array $context)
    {
        try {
            $this->entityManager->remove($this->retrieve($resourceClass, $id, $context));
            $this->entityManager->flush();
        } catch (ConstraintViolationException $foreignKeyConstraintViolationException) {
            throw new RemoveConflictException($id, $foreignKeyConstraintViolationException);
        }
    }

    /**
     * Retrieves a single resource by some identifier.
     *
     * @param string $resourceClass
     * @param mixed $id
     * @param array $context
     * @return mixed
     */
    public function retrieve(string $resourceClass, $id, array $context)
    {
        $result = $this->entityManager->getRepository($resourceClass)->find($id);
        if (!$result) {
            throw new ResourceNotFoundException($id);
        }
        return $result;
    }

    /**
     * Retrieves a list of resources with some pagination.
     *
     * @param string $resourceClass
     * @param array $context
     * @param SearchFilterRequest $searchFilterRequest
     * @return iterable
     */
    public function retrieveAll(string $resourceClass, array $context, SearchFilterRequest $searchFilterRequest
    ): iterable {
        return $this->entityManager->getRepository($resourceClass)->findAll();
    }
}
