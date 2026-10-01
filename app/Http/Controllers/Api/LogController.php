<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    // Mengambil seluruh jejak histori perubahan status suatu order
    public function index($order_id)
    {
        $logs = Log::where('order_id', $order_id)
            ->orderBy('created_at', 'desc') // Log terbaru ditaruh paling atas
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Histori log order berhasil diambil',
            'data' => $logs
        ], 200);
    }
}
