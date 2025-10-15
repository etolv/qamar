<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;

class BaseService
{

    public function all($data = [], $withes = [], $paginated = false, $withTrashed = false)
    {
        $modelClass = $this->getCalledClass();
        $query = $modelClass::query()->with($withes);
        foreach ($data as $index => $value) {
            $query->where($index, "like", "%$value%");
        }
        if ($withTrashed) {
            $model = $query->withTrashed();
        }
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function show($id, $withes = [], $withTrashed = false)
    {
        $modelClass = $this->getCalledClass();
        $model = $modelClass::with($withes);
        if ($withTrashed) {
            $model = $model->withTrashed();
        }
        return $model->find($id);;
    }

    public function store($data)
    {
        $modelClass = $this->getCalledClass();
        $model = $modelClass::create(Arr::except($data, ['image']));
        if (isset($data['image'])) {
            $model->addMedia($data['image'])->toMediaCollection('image');
        }
        return $model;
    }

    public function update($data, $id)
    {
        $modelClass = $this->getCalledClass();
        $model = $modelClass::find($id);
        if ($model) {
            $model->update(Arr::except($data, ['image']));
            if (isset($data['image'])) {
                $model->clearMediaCollection('image');
                $model->addMedia($data['image'])->toMediaCollection('image');
            }
        }
        return $model;
    }

    public function destroy($id, $force = false)
    {
        $modelClass = $this->getCalledClass();
        $model = $modelClass::find($id);
        if ($force)
            $model->forceDelete();
        else
            $model->delete();

        return $model;
    }

    private function getCalledClass()
    {
        $calledClass = get_called_class();
        $baseClassName = class_basename($calledClass);
        $modelName = str_replace('Service', '', $baseClassName);
        $modelClass = "App\\Models\\" . $modelName;
        if (class_exists($modelClass)) {
            return $modelClass;
        } else {
            throw new Exception("Model class $modelClass does not exist.");
        }
    }
}
