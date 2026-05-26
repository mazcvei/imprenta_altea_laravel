<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductPriceUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::all();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = \App\Models\Product::with('prices')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {

            $imagePath = null;

            if ($request->hasFile('image')) {
                $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
                $imagePath = $request
                    ->file('image')
                    ->storeAs('products', $fileName, 'public');
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $imagePath,
            ]);

            foreach ($request->prices as $price) {

                $product->prices()->create([
                    'units' => $price['units'],
                    'price' => $price['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Producto creado',
                'data' => $product->load('prices')
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {

            if ($request->hasFile('image')) {

                if ($product->image) {

                    Storage::disk('public')
                        ->delete($product->image);
                }
                $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();

                $product->image = $request
                    ->file('image')
                    ->storeAs('products', $fileName, 'public');
            }

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $product->image,
            ]);

            $existingIds = collect($request->prices)
                ->pluck('id')
                ->filter();

            $product->prices()
                ->whereNotIn('id', $existingIds)
                ->delete();

            foreach ($request->prices as $price) {

                ProductPriceUnit::updateOrCreate(
                    [
                        'id' => $price['id'] ?? null
                    ],
                    [
                        'product_id' => $product->id,
                        'units' => $price['units'],
                        'price' => $price['price'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Producto actualizado',
                'data' => $product->load('prices')
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
           
            if ($product->image) {

                Storage::disk('public')
                    ->delete($product->image);
            }

            $product->prices()->delete();

            $product->delete();

            DB::commit();

            return response()->json([
                'message' => 'Producto eliminado'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
