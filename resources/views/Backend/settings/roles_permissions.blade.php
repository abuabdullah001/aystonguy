@extends('Backend.Layouts.Dashboard.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Create Role</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.roles.store') }}">
                        @csrf
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" placeholder="manager" required>
                        <button class="btn btn-primary mt-3" type="submit">Create Role</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Create Permission</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.permissions.store') }}">
                        @csrf
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" class="form-control" placeholder="manage reports" required>
                        <button class="btn btn-primary mt-3" type="submit">Create Permission</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Assign Role To User</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.assign-role') }}">
                        @csrf
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>

                        <label class="form-label mt-3">Role</label>
                        <select name="role" class="form-select" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>

                        <button class="btn btn-primary mt-3" type="submit">Assign Role</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Sync Permissions To Role</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.sync-role-permissions') }}">
                        @csrf
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>

                        <label class="form-label mt-3">Permissions</label>
                        <select name="permissions[]" class="form-select" multiple required style="min-height: 180px;">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>

                        <button class="btn btn-primary mt-3" type="submit">Sync Permissions</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Current Roles and Permissions</h5></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->permissions->pluck('name')->join(', ') ?: 'No permissions' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
