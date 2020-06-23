<?php

namespace W2w\Lib\ApieDoctrinePlugin\Normalizers;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use W2w\Lib\Apie\Exceptions\ResourceNotFoundException;
use W2w\Lib\ApieDoctrinePlugin\Exceptions\EntityNotAllowedException;

/**
 * Normalizes a primitive to an entity. If active a REST API could do:
 *
 * For example an entity that references a country entity can do this request
 *
 * {
 *     "country": 2
 * }
 *
 * To link to a country with id 2. Because of security reasons any DoctrineEntityNormalizer instance can only
 * denormalize one specific entity. You only want to apply this to entities with public data with data that
 * is almost never changed, for example country data.
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

    /**
     * Override this class method if you want to do a permission check on the entity. The normal
     * implementation is to allow all users.
     *
     * @param object $entity
     * @param array $context
     * @return bool
     */
    protected function isEntityAllowed(object $entity, array $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === $this->entityClass
            && in_array(gettype($data), ['string', 'integer', 'double', 'float', 'boolean']);
    }
}
