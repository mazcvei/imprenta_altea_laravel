<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user','items.product')->get();
        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return response()->json($order);
    }
    public function authIndex()
    {
        $orders = auth()->user()->orders()->with('items.product')->get();
        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pendiente,Procesando,Completado,Cancelado',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Estado del pedido actualizado']);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Pedido eliminado']);
    }
}
