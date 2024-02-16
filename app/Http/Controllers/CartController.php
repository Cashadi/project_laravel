<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = Cart::select('product_id', 'quantity')->where("user_id", auth()->id());

        // $data = $data->first();

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
        $user = auth()->user()->role;

        // validasi hanya users_id yang role penjual saja yang bisa bikin products
        if ($user != 'pembeli') {
            return response()->json([
                'message' => 'You are not a buyer',
            ], 404);
        }

        $validated['user_id'] = auth()->id();

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

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
