<?php

namespace W2w\Lib\ApieDoctrinePlugin\Exceptions;

use ReflectionClass;
use W2w\Lib\Apie\Exceptions\ApieException;

class EntityNotAllowedException extends ApieException
{
    public function __construct($classNameOrObject)
    {
        parent::__construct(401, (new ReflectionClass($classNameOrObject))->getShortName() . ' is not allowed!');
    }
}
