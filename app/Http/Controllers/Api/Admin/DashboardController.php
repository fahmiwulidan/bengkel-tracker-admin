<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalWorkshops' => DB::table('workshops')->count(),
            'totalMechanics' => DB::table('mechanics')->count(),
            'totalUsers' => DB::table('users')->count(),
            'totalOrders' => DB::table('orders')->count(),
        ]);
    }
}
