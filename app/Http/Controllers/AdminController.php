<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.adminPanel', compact('users'));
    }

    // Show edit form for a user
    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|in:user,admin',
            'reputation' => 'required|integer|min:0',
        ]);

        $user->update($request->only('name', 'email', 'role', 'reputation'));

        return redirect()->route('admin.adminPanel')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.adminPanel')->with('success', 'User deleted successfully.');
    }

    // Reset user password
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.adminPanel')->with('success', 'Password updated successfully.');
    }

}
