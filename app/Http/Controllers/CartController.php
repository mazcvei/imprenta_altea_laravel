<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartRequest;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function store(StoreCartRequest $request)
    {
        $user = auth()->user();
        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store('cart-images', 'public');
        }

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'price_unit_id' => $request->price_unit_id,
            ],
            [
                'image' => $imagePath
            ]
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

        $filePath = $item->image;
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $item->delete();

        return response()->json(['message' => 'Eliminado']);
    }
}
