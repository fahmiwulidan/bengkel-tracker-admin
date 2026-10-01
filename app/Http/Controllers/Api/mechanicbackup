<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mechanic;
class MechanicController extends Controller
{
    public function index()
    {
        $mechanics = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->join('workshops', 'mechanics.workshop_id', '=', 'workshops.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                CASE
                    WHEN workshops.is_open = false THEN 'close'
                    ELSE mechanics.status
                END AS status,
                users.name,
                users.phone,
                users.email,
                workshops.name AS workshop_name,
                workshops.is_open AS workshop_is_open,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude
            ")
            ->orderBy('mechanics.id')
            ->get();

        return response()->json([
            'message' => 'List mekanik berhasil diambil',
            'data' => $mechanics,
        ]);
    }

    public function showLocation($id)
    {
        $mechanic = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->join('workshops', 'mechanics.workshop_id', '=', 'workshops.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                CASE
                    WHEN workshops.is_open = false THEN 'close'
                    ELSE mechanics.status
                END AS status,
                users.name,
                users.phone,
                users.email,
                workshops.name AS workshop_name,
                workshops.is_open AS workshop_is_open,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude
            ")
            ->where('mechanics.id', $id)
            ->first();

        if (!$mechanic) {
            return response()->json([
                'message' => 'Mekanik tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Lokasi mekanik berhasil diambil',
            'data' => $mechanic,
        ]);
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $mechanic = DB::table('mechanics')->where('id', $id)->first();

        if (!$mechanic) {
            return response()->json([
                'message' => 'Mekanik tidak ditemukan',
            ], 404);
        }

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;

        DB::table('mechanics')
            ->where('id', $id)
            ->update([
                'current_position' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geography"),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Lokasi mekanik berhasil diperbarui',
            'data' => [
                'id' => (int) $id,
                'latitude' => $lat,
                'longitude' => $lng,
            ],
        ]);
    }

    public function byWorkshop($workshopId)
    {
        $mechanics = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->join('workshops', 'mechanics.workshop_id', '=', 'workshops.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                CASE
                    WHEN workshops.is_open = false THEN 'close'
                    ELSE mechanics.status
                END AS status,
                users.name,
                users.phone,
                users.email,
                workshops.name AS workshop_name,
                workshops.is_open AS workshop_is_open,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude
            ")
            ->where('mechanics.workshop_id', $workshopId)
            ->orderBy('mechanics.id')
            ->get();

        return response()->json([
            'message' => 'Mekanik bengkel berhasil diambil',
            'data' => $mechanics,
        ]);
    }

    public function nearest(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;

        $mechanics = DB::table('mechanics')
            ->join('users', 'mechanics.user_id', '=', 'users.id')
            ->join('workshops', 'mechanics.workshop_id', '=', 'workshops.id')
            ->selectRaw("
                mechanics.id,
                mechanics.user_id,
                mechanics.workshop_id,
                mechanics.status,
                users.name,
                users.phone,
                users.email,
                workshops.name AS workshop_name,
                workshops.is_open AS workshop_is_open,
                ST_Y(mechanics.current_position::geometry) AS latitude,
                ST_X(mechanics.current_position::geometry) AS longitude,
                ROUND(
                    (
                        ST_Distance(
                            mechanics.current_position,
                            ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                        ) / 1000
                    )::numeric,
                    2
                ) AS distance_km
            ", [$lng, $lat])
            ->where('mechanics.status', 'open')
            ->where('workshops.is_open', true)
            ->orderByRaw("
                ST_Distance(
                    mechanics.current_position,
                    ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
                ) ASC
            ", [$lng, $lat])
            ->get();

        return response()->json([
            'message' => 'Mekanik terdekat berhasil diambil',
            'data' => $mechanics,
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,close',
        ]);

        $mechanic = Mechanic::findOrFail($id);

        $mechanic->status = $request->status;
        $mechanic->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $mechanic->status,
        ]);
    }
}
