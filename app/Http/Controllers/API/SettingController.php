<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function show($key)
    {
        $setting = $this->settingService->valueFromKey($key, null);
        return response()->success([$key => $setting]);
    }
}
