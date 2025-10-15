<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{

    public function __construct(protected AdminService $adminService)
    {
        $this->middleware('can:read_admin')->only('index', 'fetch', 'show');
        $this->middleware('can:create_admin')->only('store', 'create');
        $this->middleware('can:update_admin')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.admin.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            User::withTrashed()->with([
                'account' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->role_id, function ($query) use ($request) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->where('id', $request->role_id);
                });
            })->onlyAdmins()->latest()
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->addColumn('role', function ($item) {
            $role = $item->roles()->first();
            return $role?->name;
        })->editColumn('phone', function ($item) {
            return $item->dial_code . $item->phone;
        })->toJson();
        return $data;
    }

    public function create()
    {
        $roles = Role::get();
        return view('dashboard.admin.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $result = $this->adminService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('admin.index');
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

    public function edit($id)
    {
        $roles = Role::all();
        $admin = User::withTrashed()->find($id);
        return view('dashboard.admin.edit', compact('roles', 'admin'));
    }

    public function update(UpdateAdminRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->adminService->update($data, $id);
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

    public function edit_profile()
    {
        $user = auth()->user();
        return redirect()->route('admin.home');
    }
}
