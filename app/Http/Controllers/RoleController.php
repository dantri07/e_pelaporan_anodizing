<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;

        $this->middleware('permission:role-list')->only('index');
        $this->middleware('permission:role-create')->only(['create', 'store']);
        $this->middleware('permission:role-edit')->only(['edit', 'update']);
        $this->middleware('permission:role-delete')->only('destroy');
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        if (request()->ajax()) {
            $roles = \Spatie\Permission\Models\Role::with('permissions')->select('roles.*');
            return \Yajra\DataTables\Facades\DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('permissions', function ($role) {
                    return $role->permissions->map(function($permission) {
                        return '<span class="badge badge-info">' . e($permission->name) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('action', function ($role) {
                    return view('roles.actions', compact('role'))->render();
                })
                ->rawColumns(['action', 'permissions'])
                ->make(true);
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $this->roleService->createRole($request->validated());
            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        try {
            $role = $this->roleService->getRoleById($id);
            $permissions = $this->roleService->getAllPermissions();
            return view('roles.edit', compact('role', 'permissions'));
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                ->with('error', 'Error fetching role: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $this->roleService->updateRole($id, $request->validated());
            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        try {
            $this->roleService->deleteRole($id);
            return response()->json(['success' => 'Role deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting role: ' . $e->getMessage()], 500);
        }
    }
} 