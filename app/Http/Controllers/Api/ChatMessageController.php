<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Validator;

class ChatMessageController extends Controller
{
    // Mengambil list riwayat chat berdasarkan Chat Room ID (Paginasi)
    public function index($chat_room_id)
    {
        $messages = ChatMessage::where('chat_room_id', $chat_room_id)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $messages
        ], 200);
    }

    // Mengirim pesan baru ke dalam chat room
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_room_id' => 'required|integer|exists:chat_rooms,id',
            'sender_id'    => 'required|integer|exists:users,id',
            'message'      => 'required|string',
            'message_type' => 'required|string|max:15', // contoh: text, image, document
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $message = ChatMessage::create([
            'chat_room_id' => $request->chat_room_id,
            'sender_id'    => $request->sender_id,
            'message'      => $request->message,
            'message_type' => $request->message_type,
            'is_read'      => false, // Pesan baru selalu default false (belum dibaca)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'data' => $message
        ], 201);
    }
}
