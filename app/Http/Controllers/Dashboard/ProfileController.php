<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\AdminService;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    public function __construct(private AdminService $adminService, private EmployeeService $employeeService) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateProfileRequest $request)
    {
        $data = $request->afterValidation();
        $result = $this->adminService->update($data, $data['id']);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $admin = User::find(auth()->id());
        $roles = Role::all();
        return view('dashboard.admin.profile', compact('roles', 'admin'));
    }

    public function dashboard()
    {
        $employee = $this->employeeService->show(auth()->user()->type_id);
        $hourly_rate = 0;
        if ($employee->activeSalary)
            $hourly_rate = $employee->activeSalary?->amount / $employee->activeSalary?->working_hours ?? 0;
        return view('dashboard.employee.show', compact('employee', 'hourly_rate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
