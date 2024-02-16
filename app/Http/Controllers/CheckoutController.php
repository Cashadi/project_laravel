<?php

namespace App\Http\Controllers;

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
        //
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
            'product.*.id' => 'required',
            'quantity' => 'integer|required',
        ]);

        // $user = auth()->user();

        // $priceProduct = Product::where('id', $request->product_id)->get()->first();
        // $validated['total_price'] = $priceProduct->price * $request->quantity;

        DB::beginTransaction();
        try {
            $hdrcheckout = HdrCheckout::create([
                'user_id' => auth()->user(),
                'total' => 0,
            ]);
        }

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
