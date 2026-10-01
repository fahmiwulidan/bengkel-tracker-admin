<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MechanicCrudController extends Controller
{
    public function index()
    {
        $mechanics = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->leftJoin('workshops', 'mechanics.workshop_id', '=', 'workshops.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                mechanics.status,
                users.name,
                users.email,
                users.phone,
                workshops.name AS workshop_name,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude
            ")
            ->orderBy('mechanics.id')
            ->get();

        $workshops = DB::table('workshops')->orderBy('name')->get();

        return view('admin.mechanics.index', compact('mechanics', 'workshops'));
    }

    public function create()
    {
        $workshops = DB::table('workshops')->orderBy('name')->get();

        return view('admin.mechanics.create', compact('workshops'));
    }

   public function store(Request $request)
{
    // Validasi
    $request->validate([
        'workshop_id' => 'required|exists:workshops,id',
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'status' => 'required|in:open,close',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
    ]);

    // Tambahkan try di sini untuk membungkus operasi database
    try {
        DB::beginTransaction();

        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'mechanic',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cek apakah user berhasil diinsert
        if (!$userId) {
            DB::rollBack(); // Harus rollback sebelum return error
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data user.'], 400);
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        DB::table('mechanics')->insert([
            'user_id' => $userId,
            'workshop_id' => $request->workshop_id,
            'status' => $request->status,
            'current_position' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography"),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // COMMIT dilakukan SETELAH semua operasi database selesai (bukan di tengah)
        DB::commit();

        return redirect()->route('admin.mechanics.index')
            ->with('success', 'Data mekanik berhasil ditambahkan.');

    } catch (\Exception $e) {
        // Jika terjadi error (misal DB mati atau duplikat data), rollback semua
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan sistem.',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
        ], 500);
    }
}


    public function edit(string $id)
{
    try {
        // Mengambil data dengan query builder
        $mechanic = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                mechanics.status,
                users.name,
                users.email,
                users.phone,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude
            ")
            ->where('mechanics.id', $id)
            ->first();

        // 1. Handling jika data tidak ditemukan (404)
        if (!$mechanic) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data mekanik tidak ditemukan.'
            ], 404);
        }

        $workshops = DB::table('workshops')->orderBy('name')->get();

        // Jika request mengharapkan JSON (misal via AJAX/API)
        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => compact('mechanic', 'workshops')
            ], 200);
        }

        return view('admin.mechanics.edit', compact('mechanic', 'workshops'));

    } catch (\Exception $e) {
        // 2. Handling jika terjadi error pada database (500)
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada server saat mengambil data.',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
    public function update(Request $request, string $id)
    {
        $mechanic = DB::table('mechanics')->where('id', $id)->first();

        abort_if(!$mechanic, 404);

        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $mechanic->user_id,
            'phone' => 'nullable',
            'password' => 'nullable|min:6',
            'status' => 'required|in:open,close',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'mechanic',
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $mechanic->user_id)
            ->update($userData);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        DB::table('mechanics')
            ->where('id', $id)
            ->update([
                'workshop_id' => $request->workshop_id,
                'status' => $request->status,
                'current_position' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography"),
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.mechanics.index')
            ->with('success', 'Data mekanik berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $mechanic = DB::table('mechanics')->where('id', $id)->first();

        abort_if(!$mechanic, 404);

        DB::table('mechanics')->where('id', $id)->delete();
        DB::table('users')->where('id', $mechanic->user_id)->delete();

        return redirect()->route('admin.mechanics.index')
            ->with('success', 'Data mekanik berhasil dihapus.');
    }
}
