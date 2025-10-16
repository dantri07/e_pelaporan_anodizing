<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
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
            $this->userService->deleteUser($id);
            return response()->json(['success' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting user: ' . $e->getMessage()], 500);
        }
    }
} 