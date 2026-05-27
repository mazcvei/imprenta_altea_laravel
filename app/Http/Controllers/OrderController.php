<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user','items.product','items.priceUnit','address')->get();
        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $user = auth()->user();

        if (
            $user->role->name !== 'admin' &&
            $order->user_id !== $user->id
        ) {
            abort(403);
        }

        $order->load([
            'user',
            'items.product',
            'items.priceUnit',
            'address',
        ]);

        return response()->json($order);
    }
    public function authIndex()
    {
        $orders = auth()->user()->orders()->with('user','items.priceUnit','items.product','address')->get();
        return response()->json($orders);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
      
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Estado del pedido actualizado']);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Pedido eliminado']);
    }
}
