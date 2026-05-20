<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $cart = Cart::with(['product', 'priceUnit'])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($cart);;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price_unit_id' => 'required|exists:product_prices_units,id',
        ]);

        $user = auth()->user();

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'price_unit_id' => $request->price_unit_id,
            ],
            []
        );

        return response()->json([
            'message' => 'Producto añadido al carrito',
            'data' => $cartItem
        ]);
    }

    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function destroy($id)
    {
        $item = Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
            
        if(!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Eliminado']);
    }
}
