<?php

namespace App\Http\Controllers\Api\Admin;

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

        // return view('admin.mechanics.index', compact('mechanics'))

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
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable',
            'password' => 'required|min:6',
            'status' => 'required|in:open,close',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $userId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'mechanic',
            'fcm_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        return redirect()->route('admin.mechanics.index')
            ->with('success', 'Data mekanik berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
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

        abort_if(!$mechanic, 404);

        $workshops = DB::table('workshops')->orderBy('name')->get();

        return view('admin.mechanics.edit', compact('mechanic', 'workshops'));
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
