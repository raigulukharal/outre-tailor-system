<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    private function markExpiredOrders()
    {
        Order::where('status', 'active')
            ->where('delivery_date', '<', Carbon::today())
            ->update(['status' => 'completed']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'serial_no' => 'required|string|unique:orders,serial_no',
            'dress_no' => 'required|integer',
            'reference_name' => 'nullable|string|max:255',
            'reference_phone' => 'nullable|string|max:20',
            'booking_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:booking_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Auto-set status based on delivery date
        $data = $request->all();
        $data['status'] = Carbon::parse($request->delivery_date)->isPast() ? 'completed' : 'active';
        
        $order = Order::create($data);
        
        return response()->json([
            'success' => true, 
            'message' => 'Order created successfully', 
            'order' => $order
        ]);
    }

    public function search(Request $request)
    {
        $query = Order::where('status', 'active');
        
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('serial_no', 'like', "%{$search}%")
                  ->orWhere('reference_name', 'like', "%{$search}%")
                  ->orWhere('reference_phone', 'like', "%{$search}%");
            });
        }
        
        $orders = $query->orderBy('delivery_date', 'asc')->get([
            'id', 'name', 'phone', 'address', 'serial_no', 'dress_no', 
            'reference_name', 'reference_phone', 'booking_date', 'delivery_date'
        ]);
        
        return response()->json(['orders' => $orders]);
    }

    public function completedOrdersView()
    {
        return view('completed.index');
    }

    public function fetchCompletedOrders()
    {
        $this->markExpiredOrders();
        $orders = Order::where('status', 'completed')
            ->orderBy('delivery_date', 'desc')
            ->get([
                'id', 'name', 'phone', 'serial_no', 'dress_no', 
                'delivery_date', 'address', 'reference_name', 
                'reference_phone', 'booking_date'
            ]);
        return response()->json(['orders' => $orders]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'dress_no' => 'required|integer',
            'delivery_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update order data
        $order->update($request->only(['name', 'phone', 'address', 'dress_no', 'delivery_date']));
        
        // IMPORTANT: Auto-update status based on new delivery date
        $newStatus = Carbon::parse($order->delivery_date)->isPast() ? 'completed' : 'active';
        $order->status = $newStatus;
        $order->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Order updated successfully', 
            'order' => $order,
            'new_status' => $newStatus
        ]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Order deleted successfully']);
    }
}