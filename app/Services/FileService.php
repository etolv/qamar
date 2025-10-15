<?php

namespace App\Services;

/**
 * Class FileService.
 */
class FileService
{
    public function storeFile($model, $file, $collection, $clear_collection = false)
    {
        if ($clear_collection)
            $model->clearMediaCollection($collection);
        return $model->addMedia($file)->toMediaCollection($collection);
    }
}
