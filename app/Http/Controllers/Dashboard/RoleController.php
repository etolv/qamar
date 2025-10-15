<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService)
    {
        $this->middleware('can:read_role')->only('index', 'show');
        $this->middleware('can:create_role')->only('store', 'create');
        $this->middleware('can:update_role')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link' => "/roles", 'name' => _t('New role')],
        ];
        $roles = $this->roleService->getAllRoles();
        $permissions = Permission::all();
        $permissions = $permissions->groupBy('group');
        return view('dashboard.role.index', compact('roles', 'permissions', 'breadcrumbs'));
    }

    public function search(Request $request)
    {
        $roles = $this->roleService->getAllRoles($request->q);
        return response()->json(['data' => $roles]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['link' => "/roles", 'name' => _t('Roles')],
            ['name' => _t('New')]
        ];
        $permissions = Permission::all();
        $permissions = $permissions->groupBy('group');
        return view('dashboard.role.add', compact('permissions', 'breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $this->roleService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('roles.index');
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

    public function edit($id)
    {
        $breadcrumbs = [
            ['link' => "/roles", 'name' => _t('Roles')],
            ['name' => _t('Edit')]
        ];
        $result = $this->roleService->edit($id);
        return view('dashboard.role.edit', ['breadcrumbs' => $breadcrumbs, 'role' => $result['role'], 'permissions' => $result['permissions'], 'rolePermissions' => $result['rolePermissions']]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $data = $request->validated();
        $this->roleService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('roles.index');
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
