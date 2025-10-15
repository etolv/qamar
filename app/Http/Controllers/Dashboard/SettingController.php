<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService)
    {
        $this->middleware('can:read_setting')->only('index', 'fetch', 'show');
        $this->middleware('can:create_setting')->only('store', 'create');
        $this->middleware('can:update_setting')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = $this->settingService->all(except: ['cash_to_points', 'points_to_cash', 'profit_percentage']);
        return view('dashboard.setting.index', compact('settings'));
    }

    public function about_us()
    {
        $settings = $this->settingService->all();
        return view('dashboard.setting.about_us', compact('settings'));
    }

    public function create()
    {
        return view('dashboard.setting.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSettingRequest $request)
    {
        $data = $request->afterValidation();
        $setting = $this->settingService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('setting.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request, $id)
    {
        $data = $request->afterValidation();
        $setting = $this->settingService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
