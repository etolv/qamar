<?php

namespace App\Services;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;

/**
 * Class SettingService.
 */
class SettingService
{
    public function __construct(protected FileService $fileService) {}
    public function all(array $keys = null, array $except = [])
    {
        return Setting::when($keys, function ($query) use ($keys) {
            $query->whereIn('key', $keys);
        })->when($keys, function ($query) use ($except) {
            $query->whereNotIn('key', $except);
        })->get();
    }

    public function fromKey($key)
    {
        return Setting::where('key', $key)->first();
    }

    public function valueFromKey($key, $default = 0)
    {
        return Setting::where('key', $key)->first()?->value ?? $default;
    }

    public function update($data, $id)
    {
        $setting = Setting::find($id);
        if ($setting->type == SettingTypeEnum::IMAGE) {
            if (isset($data['image'])) {
                $this->fileService->storeFile($setting, $data['image'], 'image', true);
            }
        } elseif ($setting->type == SettingTypeEnum::BOOLEAN) {
            $setting->update(['value' => isset($data['value']) ? true : false]);
        } else {
            $setting->update(['value' => $data['value'] ?? $setting->value]);
        }
        $setting->update(['appear_app' => $data['appear_app']]);
        return $setting;
    }

    public function store($data)
    {
        return Setting::create($data);
    }


    public function updateFromKey($key, $value, $type = 'STRING', $image = null)
    {
        $type = SettingTypeEnum::fromName($type)->value;
        $setting = Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
        if ($image) {
            $this->fileService->storeFile($setting, $image, 'image', true);
        }
        return $setting;
    }
}
