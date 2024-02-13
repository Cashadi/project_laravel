<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // tampilkan semua product tapi hanya users_id (penjual)
    public function index()
    {
        $user = auth()->user()->role;

        // kondisi untuk menampilkan users_id sesuai role nya yang login
        if ($user == "penjual") {
            $products = Product::select('id', 'name', 'description', 'price', 'image', 'category_id')->where('user_id', auth()->id());

            $products = $products->get();

            return response()->json([
                'data' => $products,
            ], 200);
        } else if ($user == 'pembeli') {
            $products = Product::select('id', 'name', 'price', 'image');

            $products = $products->get();

            return response()->json([
                'data' => $products,
            ]);
        }

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
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'image' => 'required|string',
            'category_id' => 'required|string',
        ]);

        // untuk mendapatkan id users dengan role penjual
        $user = auth()->user()->role;

        // validasi hanya users_id yang role penjual saja yang bisa bikin products
        if ($user != 'penjual') {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        $validated['user_id'] = auth()->id();

        // create::create($validated);

        return response()->json([
            'message' => 'Upload your product succesfully'
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
            'name' => 'nullable',
            'description' => 'nullable',
            'price' => 'nullable',
            'image' => 'nullable',
            'category_id' => 'nullable',
        ]);

        $user = auth()->user()->role;

        if ($user == 'penjual') {
            $data = Product::where('id', $id)->where("user_id", auth()->id())->first();

            // validasi untuk tag berdasarkan id
            if ($data == null) {
                return response()->json([
                    'messsage' => 'product not found',
                ], 404);
            }

            $data->update($validated);

            return response()->json([
                'message' => 'update your product succesfully',
            ], 200);
        } else if ($user == 'pembeli') {

            return response()->json([
                'message' => 'You are not a seller',
            ], 404);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user()->role;

        if ($user == 'penjual') {
            $data = Product::where('id', $id)->where("user_id", auth()->id())->first();

            if ($data == null) {
                return response()->json([
                    'messsage' => 'product not found',
                ], 404);
            }

            $data->delete();

            return response()->noContent();
        } else if ($user == 'pembeli') {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

    }
}
