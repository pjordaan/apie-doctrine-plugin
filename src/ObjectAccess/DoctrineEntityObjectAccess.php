<?php

namespace W2w\Lib\ApieDoctrinePlugin\ObjectAccess;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Pjordaan\AlternateReflectionExtractor\ReflectionExtractor;
use ReflectionClass;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use W2w\Lib\ApieObjectAccessNormalizer\ObjectAccess\ObjectAccess;
use W2w\Lib\ApieObjectAccessNormalizer\ObjectAccess\ObjectAccessSupportedInterface;

class DoctrineEntityObjectAccess extends ObjectAccess implements ObjectAccessSupportedInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ReflectionExtractor|DoctrineExtractor
     */
    private $doctrineTypeExtractor;

    /**
     * @param ObjectManager $objectManager
     * @param bool $publicOnly
     * @param bool $disabledConstructor
     */
    public function __construct(ObjectManager $objectManager, bool $publicOnly = true, bool $disabledConstructor = false) {
        $this->objectManager = $objectManager;
        $this->doctrineTypeExtractor = $objectManager instanceof EntityManagerInterface
            ? new DoctrineExtractor($objectManager)
            : new ReflectionExtractor();
        parent::__construct($publicOnly, $disabledConstructor);
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported(ReflectionClass $reflectionClass): bool
    {
        return $this->objectManager->getMetadataFactory()->hasMetadataFor($reflectionClass->name);
    }

    /**
     * {@inheritDoc}
     */
    public function getGetterTypes(ReflectionClass $reflectionClass, string $fieldName): array
    {
        $types = parent::getGetterTypes($reflectionClass, $fieldName);
        $doctrineTypes = $this->doctrineTypeExtractor->getTypes($reflectionClass->name, $fieldName);
        if ($doctrineTypes) {
            return $doctrineTypes + $types;
        }
        return $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getSetterTypes(ReflectionClass $reflectionClass, string $fieldName): array
    {
        $types = parent::getSetterTypes($reflectionClass, $fieldName);
        $doctrineTypes = $this->doctrineTypeExtractor->getTypes($reflectionClass->name, $fieldName);
        if ($doctrineTypes) {
            return $doctrineTypes + $types;
        }
        return $types;
    }
}
