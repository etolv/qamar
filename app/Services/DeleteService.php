<?php

namespace App\Services;

/**
 * Class DeleteService.
 */
class DeleteService
{
    public function delete($object, int $objectId)
    {
        $object = $object::class::find($objectId);
        $object->delete();
    }
}
