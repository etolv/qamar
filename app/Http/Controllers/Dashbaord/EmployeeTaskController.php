<?php

namespace App\Http\Controllers\Dashbaord;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateEmployeeTaskRequest;
use App\Services\EmployeeTaskService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class EmployeeTaskController extends Controller
{

    public function __construct(private EmployeeTaskService $employeeTaskService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(UpdateEmployeeTaskRequest $request, string $id)
    {
        $data = $request->afterValidation($id);
        $employeeTask = $this->employeeTaskService->update($data, $id);
        session()->flash('message', _t('Employee Task Updated Successfully'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
