<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nohp' => 'required|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'nohp' => $request->nohp,
        ];

        // Handle password change
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
            }
            $userData['password'] = Hash::make($request->password);
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profiles', $filename, 'public');
            $userData['foto'] = $path;
        }

        $user->update($userData);

        return redirect()->route('profile.show')->with('success', 'Data berhasil diperbarui.');
    }
}