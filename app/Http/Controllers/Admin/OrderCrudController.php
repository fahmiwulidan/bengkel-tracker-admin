<?php

namespace App\Http\Controllers\Admin;

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
}