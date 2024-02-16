<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data1 = DB::table("carts")
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->where('carts.user_id', auth()->id())
            ->select('products.name', 'products.description', 'products.price', 'products.image', 'carts.quantity')
            ->get();

        return response()->json([
            'product' => $data1,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer'
        ]);

        // untuk mendapatkan id users dengan role penjual
        $user = auth()->user();

        // validasi hanya users_id yang role penjual saja yang bisa bikin products
        if ($user->role != 'pembeli') {
            return response()->json([
                'message' => 'You are not a buyer',
            ], 404);
        }

        $priceProduct = Product::where('id', $request->product_id)->get()->first();
        $validated['user_id'] = auth()->id();
        $validated['total_price'] = $priceProduct->price * $request->quantity;

        Cart::create($validated);

        return response()->json([
            'message' => 'add product to cart succesfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $data = Cart::where('product_id', $id)->where("user_id", auth()->id())->first();

        if ($data == null) {
            return response()->json([
                'messsage' => 'your product in cart not found',
            ], 404);
        }

        $data->update($validated);

        return response()->json([
            'message' => 'quantity update succesfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Cart::where('product_id', $id)->where("user_id", auth()->id())->first();

        if ($data == null) {
            return response()->json([
                'messsage' => 'your product in cart not found',
            ], 404);
        }

        $data->delete();

        return response()->noContent();
    }
}
