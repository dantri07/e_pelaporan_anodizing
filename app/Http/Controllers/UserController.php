<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\MachineReport;
use App\Models\Action;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('permission:user-list')->only('index');
        $this->middleware('permission:user-create')->only(['create', 'store']);
        $this->middleware('permission:user-edit')->only(['edit', 'update']);
        $this->middleware('permission:user-delete')->only('destroy');
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        if (request()->ajax()) {
            $users = \App\Models\User::with('roles')->select('users.*');
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('roles', function ($user) {
                    return $user->roles->map(function($role) {
                        return '<span class="badge badge-info">' . e($role->name) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('action', function ($user) {
                    return view('users.actions', compact('user'))->render();
                })
                ->rawColumns(['action', 'roles'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = $this->userService->getAllRoles();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->createUser($request->validated());
            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            $roles = $this->userService->getAllRoles();
            return view('users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Error fetching user: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $this->userService->updateUser($id, $request->validated());
            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage.
     */
   public function destroy($id)
    {
    try {
        $user = \App\Models\User::findOrFail($id);
        $reportCount = $user->machineReports()->count();
        if ($reportCount > 0) {
                return response()->json([
                    'error' => 'User cannot be deleted because they have created ' . $reportCount . ' machine report(s).'
                ], 409); 
        }
        $actionCount = $user->actions()->count();
        if ($actionCount > 0) {
                return response()->json([
                    'error' => 'User cannot be deleted because they have created ' . $actionCount . ' action(s).'
                ], 409); 
        }
        if (auth()->id() == $id) {
                return response()->json([
                    'error' => 'You cannot delete yourself.'
                ], 403); // 403 Forbidden
        }
        $this->userService->deleteUser($id);
        return response()->json(['success' => 'User deleted successfully.']);
    

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Tangani jika ID User tidak ditemukan
            return response()->json(['error' => 'User not found.'], 404);
        }
      catch (\Exception $e) {
        \Log::error('Error deleting user ID ' . $id . ': ' . $e->getMessage());
        return response()->json(['error' => 'Error deleting user: ' . $e->getMessage()], 500);
    }
}
}