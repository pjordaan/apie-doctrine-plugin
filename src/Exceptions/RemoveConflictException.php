<?php


namespace W2w\Lib\ApieDoctrinePlugin\Exceptions;

use Throwable;
use W2w\Lib\Apie\Exceptions\ApieException;

/**
 * Exception thrown when an entity can not be removed because of a foreign key constraint.
 */
class RemoveConflictException extends ApieException
{
    public function __construct($id, ?Throwable $previous = null)
    {
        parent::__construct(409, 'Can not remove ' . $id . ' as it is in use by a different resource', $previous);
    }
}
