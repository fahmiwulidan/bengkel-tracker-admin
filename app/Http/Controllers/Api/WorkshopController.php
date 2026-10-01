<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkshopController extends Controller
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
            ->get();

        return response()->json([
            'message' => 'List bengkel berhasil diambil',
            'data' => $workshops,
        ]);

    }
    public function show($id)
    {
        try {
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
                ->first(); // Mengambil satu baris data

            // JIKA DATA TIDAK DITEMUKAN (ERROR 404)
            if (!$workshop) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Bengkel dengan ID ' . $id . ' tidak ditemukan'
                ], 404);
            }

            // JIKA DATA BERHASIL DITEMUKAN (STATUS 200)
            return response()->json([
                'status'  => 'success',
                'message' => 'Detail bengkel berhasil diambil',
                'data'    => $workshop,
            ], 200);

        } catch (\Exception $e) {
            // JIKA TERJADI MASALAH DATABASE/SERVER (ERROR 500)
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal pada server',
                'debug'   => $e->getMessage()
            ], 500);
        }
    }

    public function nearest(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        $workshops = DB::table('workshops')
            ->selectRaw("
                id,
                name,
                address,
                rating,
                is_open,
                ST_Y(geom::geometry) AS latitude,
                ST_X(geom::geometry) AS longitude,
                ROUND(
                    (
                        ST_Distance(
                            geom,
                            ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                        ) / 1000
                    )::numeric,
                    2
                ) AS distance_km
            ", [$lng, $lat])
            ->orderBy('distance_km')
            ->get();

        return response()->json([
            'message' => 'Bengkel terdekat berhasil diambil',
            'data' => $workshops,
        ]);
    }

    public function open()
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
            ->where('is_open', true)
            ->get();

        return response()->json([
            'message' => 'Bengkel buka berhasil diambil',
            'data' => $workshops,
        ]);
    }

    public function topRated(Request $request)
    {
        $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        $lat = $request->lat ?? -7.1187;
        $lng = $request->lng ?? 112.4215;

        $workshops = DB::table('workshops')
            ->selectRaw("
                id,
                name,
                address,
                rating,
                is_open,
                ST_Y(geom::geometry) AS latitude,
                ST_X(geom::geometry) AS longitude,
                ROUND(
                    (
                        ST_Distance(
                            geom,
                            ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                        ) / 1000
                    )::numeric,
                    2
                ) AS distance_km
            ", [$lng, $lat])
            ->orderByDesc('rating')
            ->get();

        return response()->json([
            'message' => 'Bengkel rating tertinggi berhasil diambil',
            'data' => $workshops,
        ]);
    }

    public function filter(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'is_open' => 'nullable|boolean',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $radius = $request->radius ?? 10;

        $query = DB::table('workshops')
            ->selectRaw("
                id,
                name,
                address,
                rating,
                is_open,
                ST_Y(geom::geometry) AS latitude,
                ST_X(geom::geometry) AS longitude,
                ROUND(
                    (
                        ST_Distance(
                            geom,
                            ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                        ) / 1000
                    )::numeric,
                    2
                ) AS distance_km
            ", [$lng, $lat])
            ->whereRaw("
                ST_DWithin(
                    geom,
                    ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography,
                    ?
                )
            ", [$lng, $lat, $radius * 1000]);

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->filled('is_open')) {
            $query->where('is_open', $request->boolean('is_open'));
        }

        $workshops = $query
            ->orderByRaw("
                ST_Distance(
                    geom,
                    ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                ) ASC
            ", [$lng, $lat])
            ->get();

        return response()->json([
            'message' => 'Filter bengkel berhasil diambil',
            'data' => $workshops,
        ]);
    }
}
