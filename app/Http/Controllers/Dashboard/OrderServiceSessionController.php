<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSessionRequest;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateServiceSessionEmployeeRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Services\OrderServiceService;
use App\Services\OrderServiceSessionService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderServiceSessionController extends Controller
{

    public function __construct(
        protected OrderServiceService $orderServiceService,
        protected OrderServiceSessionService $orderServiceSessionService,
        protected SettingService $settingService
    ) {
        $this->middleware('can:read_order')->only('index', 'fetch', 'show');
        $this->middleware('can:create_order')->only('store', 'create');
        $this->middleware('can:update_order')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function create(CreateSessionRequest $request)
    {
        $data = $request->afterValidation();
        $order_service = $this->orderServiceService->show($data['service_id']);
        return view('dashboard.order.listed.add', compact('order_service'));
    }

    public function pdf($id)
    {
        $order_service = $this->orderServiceService->show($id);
        $font_family = "'Roboto','sans-serif'";
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
        $config = [];
        if (session('locale') == 'ar') {
            $font_family = "'Baloo Bhaijaan 2','sans-serif'";
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }
        $settings = $this->settingService->all();
        $pageConfigs = ['myLayout' => 'blank'];
        // pdf.invoice
        $pdf = Pdf::loadView('dashboard.order.listed.add', [
            'order_service' => $order_service,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'settings' => $settings,
            'pageConfigs' => $pageConfigs
        ]);
        return $pdf->stream("listed-order-$order_service->id-" . now() . '.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request)
    {
        $data = $request->afterValidation();
        $session = $this->orderServiceSessionService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function update_employee(UpdateServiceSessionEmployeeRequest $request)
    {
        $data = $request->AfterValidation();
        $session = $this->orderServiceSessionService->update(Arr::only($data, ['employee_id']), $data['session_id']);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order_service = $this->orderServiceService->show($id);
        return view('dashboard.order.listed.add', compact('order_service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $session = $this->orderServiceSessionService->update($data, $id);
        // session()->flash('message', _t('Success'));
        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
