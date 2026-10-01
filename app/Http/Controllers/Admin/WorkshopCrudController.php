<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkshopCrudController extends Controller
{
    public function index()
    {
        $workshops = DB::table('workshops')
            ->selectRaw("
                id,
                name,
                address,
                rating,
                is_open,
                ST_Y(geom::geometry) AS latitude,
                ST_X(geom::geometry) AS longitude
            ")
            ->orderBy('id')
            ->get();

        return view('admin.workshops.index', compact('workshops'));
    }

    public function create()
    {
        return view('admin.workshops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'rating' => 'required|numeric|min:0|max:5',
            'is_open' => 'required|boolean',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        DB::table('workshops')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'rating' => $request->rating,
            'is_open' => $request->is_open,
            'geom' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography"),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Data bengkel berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $workshop = DB::table('workshops')
            ->selectRaw("
                id,
                name,
                address,
                rating,
                is_open,
                ST_Y(geom::geometry) AS latitude,
                ST_X(geom::geometry) AS longitude
            ")
            ->where('id', $id)
            ->first();

        abort_if(!$workshop, 404);

        return view('admin.workshops.edit', compact('workshop'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'rating' => 'required|numeric|min:0|max:5',
            'is_open' => 'required|boolean',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        DB::table('workshops')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'address' => $request->address,
                'rating' => $request->rating,
                'is_open' => $request->is_open,
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography"),
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Data bengkel berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        DB::table('workshops')->where('id', $id)->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Data bengkel berhasil dihapus.');
    }
}