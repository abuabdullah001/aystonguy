<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function index(): View
    {
        return view('Backend.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            $path = 'uploads/profile_photo/';
            if (! is_dir(public_path($path))) {
                mkdir(public_path($path), 0777, true);
            }

            if (! empty($user->profile_image) && file_exists(public_path($user->profile_image))) {
                @unlink(public_path($user->profile_image));
            }

            $file = $request->file('profile_image');
            $fileName = Str::uuid()->toString().'_'.$user->id.'.'.$file->getClientOriginalExtension();
            $file->move(public_path($path), $fileName);
            $validated['profile_image'] = $path.$fileName;
        }

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}
