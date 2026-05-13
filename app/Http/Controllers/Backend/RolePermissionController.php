<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        return view('Backend.settings.roles_permissions', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'users' => User::query()->with('roles')->orderBy('name')->get(),
        ]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
        ]);

        Role::query()->create(['name' => $validated['name']]);

        return back()->with('status', 'Role created successfully.');
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
        ]);

        Permission::query()->create(['name' => $validated['name']]);

        return back()->with('status', 'Permission created successfully.');
    }

    public function assignRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $user->syncRoles([$validated['role']]);

        return back()->with('status', 'Role assigned successfully.');
    }

    public function syncPermissionToRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'exists:permissions,name'],
        ]);

        $role = Role::query()->where('name', $validated['role'])->firstOrFail();
        $role->syncPermissions($validated['permissions']);

        return back()->with('status', 'Role permissions synced.');
    }
}
