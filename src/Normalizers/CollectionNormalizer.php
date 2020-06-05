<?php

namespace W2w\Lib\ApieDoctrinePlugin\Normalizers;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use W2w\Lib\ApieObjectAccessNormalizer\Normalizers\ApieObjectAccessNormalizer;

/**
 * Normalizes Doctrine Collection instances.
 */
class CollectionNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $collectionType = $this->getArrayCollectionType($context);
        if ($collectionType !== null) {
            unset($context['object_to_populate']);
            return new ArrayCollection($this->serializer->denormalize($data, $collectionType . '[]', $format, $context));
        }
        return new ArrayCollection($data);
    }

    /**
     * Figure out type of Collection type.
     *
     * @see ApieObjectAccessNormalizer
     *
     * @param array $context
     * @return string|null
     */
    private function getArrayCollectionType(array $context): ?string
    {
        if (array_key_exists('collection_resource', $context)) {
            return $context['collection_resource'];
        }
        $last = $context['object_hierarchy'][array_key_last($context['object_hierarchy'])];
        $keys = explode('.', $context['key_prefix']);
        $lastKey = $keys[array_key_last($keys)];
        $metadata = $this->objectManager->getMetadataFactory()->getMetadataFor(get_class($last));
        $type = $metadata->getTypeOfField($lastKey);
        if ($type === null) {
            $type = $metadata->getAssociationTargetClass($lastKey);
        }
        return $type;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Collection::class || $type === ArrayCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $result = [];
        foreach ($object as $value) {
            $result[] = $this->serializer->normalize($value, $format, $context);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Collection;
    }
}
