<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\DtlCheckout;
use App\Models\HdrCheckout;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $history = HdrCheckout::select('total', 'created_at as Date')->where('user_id', auth()->id())->get();

        return response()->json([
            'History' => $history,
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
            'products' => 'array|required',
            'products.*.id' => 'required',
            'products.*.quantity' => 'integer|nullable',
        ]);
        $products = $validated['products'];

        DB::beginTransaction();
        try {
            $hdrcheckout = HdrCheckout::create([
                'user_id' => auth()->user()->id,
                'total' => 0,
            ]);

            $total_price = 0;

            foreach ($products as $product) {
                if (isset($product['quantity'])) {
                    $dt = [
                        'hdr_checkout_id' => $hdrcheckout->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                    ];
                } else {
                    $cart = Cart::where('product_id', $product['id'])->first();

                    $dt = [
                        'hdr_checkout_id' => $hdrcheckout->id,
                        'product_id' => $product['id'],
                        'quantity' => $cart->quantity,
                    ];
                }

                $detailProduct = Product::where('id', $product['id'])->get()->first();
                $dt['price'] = $detailProduct->price * $dt['quantity'];

                $stock = $detailProduct->stock;

                $detailProduct->update([
                    'stock' => $stock - $dt['quantity'],
                ]);

                DtlCheckout::create($dt);

                $cart = Cart::where('product_id', $product['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if ($cart) {
                    $cart->delete();
                }

                $total_price += $dt['price'];
            }

            $hdrcheckout->update(['total' => $total_price]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 503);
        }

        return response()->json([
            'message' => 'checkout your product succesfully'
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
