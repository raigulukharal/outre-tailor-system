<?php
// app/Http/Controllers/ReminderController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function reminders(Request $request)
    {
        // Set Pakistan timezone
        date_default_timezone_set('Asia/Karachi');
        
        // Get tomorrow's date
        $tomorrow = Carbon::now('Asia/Karachi')->addDay()->toDateString();
        
        // Fetch only ACTIVE orders for tomorrow
        $orders = DB::table('orders')
            ->where('status', 'active')
            ->whereDate('delivery_date', $tomorrow)
            ->select(
                'id', 'name', 'phone', 'serial_no', 'dress_no', 
                'delivery_date', 'address', 'reference_name', 'reference_phone'
            )
            ->orderBy('name', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'orders' => $orders,
            'debug' => [
                'tomorrow' => $tomorrow,
                'timezone' => date_default_timezone_get(),
                'orders_found' => $orders->count()
            ]
        ]);
    }
}