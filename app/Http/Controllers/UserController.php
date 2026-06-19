<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Technician;
use App\Mail\TechnicianVerifyEmailMail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', 'in:admin,technician'],
            'photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($request->role === 'technician') {
            $rules['phone_number']   = ['required', 'string', 'max:20'];
            $rules['specialization'] = ['nullable', 'in:installation,repair,both'];
            $rules['area_coverage']  = ['nullable', 'string', 'max:255'];
        }

        $request->validate($rules);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('users', 'public');
        }

        // Technicians start unverified (email_verified_at = null)
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'photo'             => $photo,
            'email_verified_at' => $request->role === 'admin' ? now() : null,
        ]);

        if ($request->role === 'technician') {
            $verificationToken = Str::random(64);

            $technician = Technician::create([
                'user_id'                  => $user->id,
                'name'                     => $user->name,
                'email'                    => $user->email,
                'status'                   => 'available',
                'phone_number'             => $request->phone_number,
                'specialization'           => $request->specialization,
                'area_coverage'            => $request->area_coverage,
                'photo'                    => $photo,
                'email_verification_token' => $verificationToken,
                'email_verified_at'        => null,
            ]);

            try {
                Mail::to($technician->email)->send(new TechnicianVerifyEmailMail($technician));
            } catch (\Exception $e) {
                \Log::error('Failed to send technician verification email: ' . $e->getMessage());
            }

            return redirect()->route('users.index')->with('success', "Technician '{$user->name}' created. A verification email has been sent to {$user->email}. Status is pending until verified.");
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
