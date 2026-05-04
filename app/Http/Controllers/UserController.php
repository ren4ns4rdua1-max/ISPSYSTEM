<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', 'in:admin,technician'],
            'photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('users', 'public');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'photo'    => $photo,
        ]);

        if ($request->role === 'technician') {
            Technician::create([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'status'         => 'available',
                'phone_number'   => '',
                'specialization' => '',
                'area_coverage'  => '',
                'photo'          => $photo,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role'  => ['sometimes', 'in:admin,technician'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $oldRole = $user->role;
        $newRole = $request->role ?? $user->role;

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $newRole,
        ];

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($data);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($newRole === 'technician') {
            Technician::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name, 'email' => $user->email, 'status' => 'available', 'phone_number' => '', 'specialization' => '', 'area_coverage' => '']
            );
            Technician::where('user_id', $user->id)->update([
                'name'  => $user->name,
                'email' => $user->email,
                'photo' => $user->photo,
            ]);
        } elseif ($oldRole === 'technician' && $newRole !== 'technician') {
            Technician::where('user_id', $user->id)->delete();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->photo) Storage::disk('public')->delete($user->photo);
        Technician::where('user_id', $user->id)->delete();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
