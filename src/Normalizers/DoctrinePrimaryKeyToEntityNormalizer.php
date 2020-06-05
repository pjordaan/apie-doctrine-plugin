<?php

namespace W2w\Lib\ApieDoctrinePlugin\Normalizers;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use W2w\Lib\Apie\Exceptions\ResourceNotFoundException;
use W2w\Lib\ApieDoctrinePlugin\Exceptions\EntityNotAllowedException;

/**
 * Normalizes a primitive to an entity. If active a REST API could do:
 *
 * For example an entitiy that references a country entitiy can do this request
 *
 * {
 *     "country": 2
 * }
 *
 * To link to a country with id 2. Because of security reasons any DoctrineEntityNormalizer instance can only
 * denormalize one specific entity. You only want to apply this to entities with public data with data that
 * is almost never changed.
 */
class DoctrinePrimaryKeyToEntityNormalizer implements DenormalizerInterface
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(string $entityClass, ObjectManager $objectManager)
    {
        $this->entityClass = $entityClass;
        $this->objectManager = $objectManager;
    }

    protected function isEntityAllowed(object $entity, array $context): bool
    {
        return true;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $result = $this->objectManager->getRepository($type)->find($data);
        if (!$result) {
            throw new ResourceNotFoundException($type);
        }
        if (!$this->isEntityAllowed($result, $context)) {
            throw new EntityNotAllowedException($result);
        }
        return $result;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === $this->entityClass
            && in_array(gettype($data), ['string', 'integer', 'double', 'float', 'boolean']);
    }
}
