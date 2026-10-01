<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCrudController extends Controller
{
    public function index()
    {
        $users = DB::table('users')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:customer,mechanic,admin',
            'password' => 'required|min:6',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'fcm_token' => null,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->first();

        abort_if(!$user, 404);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:customer,mechanic,admin',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil dihapus.');
    }
}
