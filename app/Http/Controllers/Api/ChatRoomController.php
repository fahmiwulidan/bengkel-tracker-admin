<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // ✅ 1. WAJIB TAMBAHKAN INI AGAR TIDAK ERROR 500

class ChatRoomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Mendapatkan user aktif dari Bearer Token Sanctum

        // Periksa apakah user yang sedang login terdaftar sebagai mekanik
        $mechanic = DB::table('mechanics')->where('user_id', $user->id)->first();

        // Mengambil room chat secara adaptif berdasarkan role yang memanggil
        $chatRooms = ChatRoom::whereHas('order', function ($query) use ($user, $mechanic) {
            if ($mechanic) {
                // Jika Mekanik, filter order berdasarkan mechanic_id-nya
                $query->where('mechanic_id', $mechanic->id);
            } else {
                // Jika Customer, filter order berdasarkan user_id-nya
                $query->where('user_id', $user->id);
            }
        })
        ->with(['order.mechanic', 'order.workshop', 'order.user']) // Eager load relasi
        ->get()
        ->map(function ($room) {
            // Ambil pesan terakhir untuk ditampilkan di sub-title list chat
            $lastMessage = \App\Models\ChatMessage::where('chat_room_id', $room->id)
                ->orderBy('created_at', 'desc')
                ->first();

            return [
                'id' => $room->id,
                'order_id' => $room->order_id,
                'order_code' => $room->order?->order_code ?? '-',
                'status' => $room->order?->status ?? 'pending',
                // ✅ 2. Gunakan ?-> (Nullsafe) agar tidak memicu 500 error jika relasinya kosong
                'customer_name' => $room->order?->user?->name ?? 'Pelanggan',
                'workshop_name' => $room->order?->workshop_name ?? ($room->order?->workshop?->name ?? null),
                'mechanic_name' => $room->order?->mechanic_name ?? ($room->order?->mechanic?->name ?? 'Mekanik'),
                'last_message' => $lastMessage ? $lastMessage->message : 'Belum ada pesan',
                'updated_at_formatted' => $room->updated_at ? $room->updated_at->format('H:i') : now()->format('H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar chat room berhasil diambil',
            'data' => $chatRooms
        ], 200);
    }

    // Membuat room chat baru berdasarkan order_id
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $chatRoom = ChatRoom::firstOrCreate([
            'order_id' => $request->order_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat room berhasil dimuat/dibuat',
            'data' => $chatRoom
        ], 201);
    }

    // Mengambil detail chat room berdasarkan ID
    public function show($id)
    {
        $chatRoom = ChatRoom::with('order')->find($id);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $chatRoom
        ], 200);
    }
}
