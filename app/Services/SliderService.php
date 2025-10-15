<?php

namespace App\Services;

use App\Models\Slider;

/**
 * Class SliderService.
 */
class SliderService
{
    public function all($withTrashed = false)
    {
        return Slider::when($withTrashed, function ($query) {
            $query->withTrashed();
        })->get();
    }

    public function show($id)
    {
        return Slider::withTrashed()->find($id);
    }

    public function store($data)
    {
        $image = $data['image'];
        unset($data['image']);
        $slider = Slider::create($data);
        if ($image) {
            $slider->clearMediaCollection('image');
            $slider->addMedia($image)->toMediaCollection('image');
        }
        return $slider;
    }

    public function update($data, $id)
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $slider = Slider::withTrashed()->find($id);
        $slider->update($data);
        if ($image) {
            $slider->clearMediaCollection('image');
            $slider->addMedia($image)->toMediaCollection('image');
        }
        return $slider;
    }
}
