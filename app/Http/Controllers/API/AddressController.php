<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyAddressRequest;
use App\Http\Requests\IndexAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function __construct(private AddressService $addressService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexAddressRequest $request)
    {
        $data = $request->validated();
        $addresses = $this->addressService->all(data: $data, paginated: true, withes: ['municipal']);
        $data = AddressResource::collection($addresses);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $address = $this->addressService->store(data: $data);
        return response()->success(AddressResource::make($address));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, string $id)
    {
        $data = $request->validated();
        $address = $this->addressService->update(data: $data, id: $id);
        return response()->success(AddressResource::make($address));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyAddressRequest $request, string $id)
    {
        $data = $request->validated();
        $address = $this->addressService->destroy(id: $id);
        return response()->success();
    }
}
