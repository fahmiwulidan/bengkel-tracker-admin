<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderCrudController extends Controller
{
    public function index()
    {
        $orders = DB::table('orders')
            ->join('users as customer_user', 'orders.user_id', '=', 'customer_user.id')
            ->leftJoin('workshops', 'orders.workshop_id', '=', 'workshops.id')
            ->leftJoin('mechanics', 'orders.mechanic_id', '=', 'mechanics.id')
            ->leftJoin('users as mechanic_user', 'mechanics.user_id', '=', 'mechanic_user.id')
            ->select(
                'orders.id',
                'orders.order_code',
                'orders.status',
                'orders.problem',
                'orders.basic_cost',
                'orders.total_cost',
                'orders.eta',
                'orders.created_at',
                'customer_user.name as customer_name',
                'workshops.name as workshop_name',
                'mechanic_user.name as mechanic_name'
            )
           
            ->orderByDesc('orders.id')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->join('users as customer_user', 'orders.user_id', '=', 'customer_user.id')
            ->leftJoin('workshops', 'orders.workshop_id', '=', 'workshops.id')
            ->leftJoin('mechanics', 'orders.mechanic_id', '=', 'mechanics.id')
            ->leftJoin('users as mechanic_user', 'mechanics.user_id', '=', 'mechanic_user.id')
            ->where('orders.id', $id)
            ->selectRaw("
                orders.id,
                orders.order_code,
                orders.status,
                orders.problem,
                orders.basic_cost,
                orders.total_cost,
                orders.eta,
                orders.created_at,

                customer_user.id AS user_id,
                customer_user.name AS customer_name,
                customer_user.phone AS customer_phone,
                ST_Y(orders.user_location::geometry) AS user_latitude,
                ST_X(orders.user_location::geometry) AS user_longitude,

                workshops.id AS workshop_id,
                workshops.name AS workshop_name,
                workshops.address AS workshop_address,
                workshops.is_open AS workshop_is_open,
                ST_Y(workshops.geom::geometry) AS workshop_latitude,
                ST_X(workshops.geom::geometry) AS workshop_longitude,

                mechanics.id AS mechanic_id,
                CASE
                    WHEN workshops.is_open = false THEN 'close'
                    ELSE mechanics.status
                END AS mechanic_status,
                mechanic_user.name AS mechanic_name,
                mechanic_user.phone AS mechanic_phone,
                ST_Y(mechanics.current_position::geometry) AS mechanic_latitude,
                ST_X(mechanics.current_position::geometry) AS mechanic_longitude
            ")
            ->first();

        abort_if(!$order, 404);

        return view('admin.orders.show', compact('order'));
    }

     public function accept($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        // Cek apakah masih ada order aktif
        $activeOrder = DB::table('orders')
            ->whereIn('status', [
                'on_the_way',
                'service'
            ])
            ->first();

        if ($activeOrder) {
            return response()->json([
                'message' => 'Masih ada order yang sedang dikerjakan',
            ], 422);
        }

        DB::table('orders')
            ->where('id', $id)
            ->update([
                'status' => 'on_the_way',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Order diterima, mekanik menuju lokasi',
        ]);
    }

    public function arrive($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        DB::table('orders')
            ->where('id', $id)
            ->update([
                'status' => 'service',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Mekanik tiba di lokasi dan servis dimulai',
        ]);
    }

    public function complete($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        DB::table('orders')
            ->where('id', $id)
            ->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Servis selesai',
        ]);
    }
    public function mechanicOrders()
    {
        $orders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('mechanics', 'orders.mechanic_id', '=', 'mechanics.id')
            ->whereIn('orders.status', [
                'pending',
                'on_the_way',
                'service'
            ])
            ->select(
                'orders.id',
                'orders.order_code',
                'orders.status',
                'orders.problem',
                'orders.basic_cost',
                'orders.total_cost',
                'orders.created_at',

                'users.name as customer_name',
                'users.phone as customer_phone',

                DB::raw('ST_Y(orders.user_location::geometry) as user_latitude'),
                DB::raw('ST_X(orders.user_location::geometry) as user_longitude'),

                'mechanics.id as mechanic_id'
            )
            ->orderByDesc('orders.created_at')
            ->get();

        return response()->json([
            'message' => 'Daftar order berhasil diambil',
            'data' => $orders
        ]);
    }
}
