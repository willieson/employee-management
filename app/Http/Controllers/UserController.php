<?php

namespace App\Http\Controllers;

use App\Models\Annual_leaves;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index()
    {
        $user_list = User::with('superior')->paginate(5);
        // Data untuk dropdown superior (semua user)
        $all_users = User::all(['id', 'name']); // Hanya ambil id dan name untuk efisiensi
        return view('employee', compact('user_list', 'all_users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Staff,HRD,Manager',
            'id_superior' => 'nullable|exists:users,id',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'address' => $request->address,
                'contact' => $request->contact,
                'email' => $request->email,
                'role' => $request->role,
                'id_superior' => $request->id_superior ?: null,
                'password' => Hash::make($request->password),
            ]);

            // Tentukan total_leave berdasarkan role
            $total_leave = match ($user->role) {
                'Staff' => 12,
                'HRD' => 14,
                'Manager' => 18,
                default => 12, // Default jika role tidak sesuai
            };

            // Insert ke tabel annual_leaves
            Annual_leaves::create([
                'users_id' => $user->id,
                'year' => now()->year, // Tahun saat ini
                'total_leave' => $total_leave,
                'remaining_leave' => 0,
            ]);
            return redirect()->route('employee')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
}
