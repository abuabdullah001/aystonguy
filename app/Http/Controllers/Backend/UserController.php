<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(): View
    {
        return view('Backend.users.index', [
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $users = User::query()->with('roles')->latest();

        return DataTables::eloquent($users)
            ->addColumn('roles', fn (User $user) => $user->roles->pluck('name')->join(', '))
            ->addColumn('action', function (User $user) {
                return '<button class="btn btn-sm btn-primary js-edit-user" data-id="'.$user->id.'">Edit</button>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
        ]);

        if (! empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
            ],
        ]);
    }

    public function edit(User $user): JsonResponse
    {
        $user->loadMissing('roles');

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->roles->first()?->name,
            ],
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $updatePayload = [
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updatePayload['password'] = $validated['password'];
        }

        $user->update($updatePayload);

        if (! empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
        ]);
    }
}
