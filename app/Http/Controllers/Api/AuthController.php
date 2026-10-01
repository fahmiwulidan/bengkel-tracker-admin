<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi inputan berdasarkan skema database baru
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
            'role' => 'required|string', // Sesuaikan opsi enum role Anda di frontend nanti
            'fcm_token' => 'nullable|string', // Opsional, boleh kosong saat registrasi awal
        ]);

        // 2. Simpan data user ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Otomatis ter-hash oleh cast model
            'phone' => $request->phone,
            'role' => $request->role,
            'fcm_token' => $request->fcm_token,
        ]);

        // 3. Generate Bearer Token otomatis
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'nullable|string', // Tambahkan ini jika ingin mengupdate token push notifikasi tiap user login
        ]);

        $user = User::where('email', $request->email)->first();

        // Validasi user dan password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial yang Anda masukkan salah.'
            ], 401);
        }

        // Opsional: Jika fcm_token dikirim saat login dari mobile device, update datanya di database
        if ($request->has('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        // Generate Bearer Token
        $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json([
    'success' => true,
    'message' => 'Login berhasil',
    'access_token' => $token,
    'token_type' => 'Bearer',
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ]
]);
    }

    public function logout(Request $request)
    {
        // Menghapus token bearer yang sedang aktif saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout, token telah dihapus.'
        ]);
    }
}
