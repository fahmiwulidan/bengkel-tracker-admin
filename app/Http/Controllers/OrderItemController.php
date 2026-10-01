<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    // Menampilkan seluruh item atau sparepart yang dibeli dalam satu order
    public function index($order_id)
    {
        $items = OrderItem::where('order_id', $order_id)->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ], 200);
    }

    // Menambahkan item baru (misal: mekanik menambahkan sparepart tambahan saat servis berjalan)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'name'     => 'required|string|max:100',
            'price'    => 'required|integer|min:0',
            'qty'      => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $item = OrderItem::create([
            'order_id' => $request->order_id,
            'name'     => $request->name,
            'price'    => $request->price,
            'qty'      => $request->qty,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke dalam order',
            'data' => $item
        ], 201);
    }
}
