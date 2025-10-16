<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;

        $this->middleware('permission:permission-list')->only('index');
        $this->middleware('permission:permission-create')->only(['create', 'store']);
        $this->middleware('permission:permission-edit')->only(['edit', 'update']);
        $this->middleware('permission:permission-delete')->only('destroy');
    }

    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        if (request()->ajax()) {
            $permissions = \Spatie\Permission\Models\Permission::query();
            return \Yajra\DataTables\Facades\DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('action', function ($permission) {
                    return view('permissions.actions', compact('permission'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('permissions.index');
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        try {
            $this->permissionService->createPermission($request->validated());
            return redirect()->route('permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating permission: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit($id)
    {
        try {
            $permission = $this->permissionService->getPermissionById($id);
            return view('permissions.edit', compact('permission'));
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')
                ->with('error', 'Error fetching permission: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        try {
            $this->permissionService->updatePermission($id, $request->validated());
            return redirect()->route('permissions.index')
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating permission: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy($id)
    {
        try {
            $this->permissionService->deletePermission($id);
            return response()->json(['success' => 'Permission deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting permission: ' . $e->getMessage()], 500);
        }
    }
} 