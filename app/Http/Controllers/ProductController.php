<?php

namespace App\Http\Controllers;

use App\Models\HdrCheckout;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // tampilkan semua product tapi hanya users_id (penjual)
    public function index()
    {
        $user = auth()->user();

        // kondisi untuk menampilkan users_id sesuai role nya yang login
        if ($user->role == "penjual") {
            $filterProductByCategory = request()->query('search');
            $products = Product::select('products.name', 'products.price', 'products.stock', 'products.image')
                ->join('products_categories', 'products.id', '=', 'products_categories.product_id')
                ->join('categories', 'products_categories.category_id', '=', 'categories.id')
                ->where('products.user_id', auth()->id());

            if ($filterProductByCategory != null) {
                $products->where('categories.name', $filterProductByCategory);
            }

            $products->groupBy('products.name', 'products.price', 'products.stock', 'products.image');

            $products = $products->get();

            return response()->json([
                'data' => $products,
            ], 200);

        } else if ($user->role == 'pembeli') {
            $filterProductByCategory = request()->query('search');
            $products = Product::select('products.id', 'products.name', 'products.price', 'products.stock', 'products.image')
            ->join('products_categories', 'products.id', '=', 'products_categories.product_id')
            ->join('categories', 'products_categories.category_id', '=', 'categories.id');

            if ($filterProductByCategory != null) {
                $products->where('categories.name', $filterProductByCategory);
            }
            $products->groupBy('products.id', 'products.name', 'products.price', 'products.stock', 'products.image');

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
            'stock' => 'required|integer',
            'categories' => 'array|required',
            'category.*.id' => 'required',
        ]);

        // untuk mendapatkan id users dengan role penjual
        $user = auth()->user();

        // validasi hanya users_id yang role penjual saja yang bisa bikin products
        if ($user->role != 'penjual') {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        $validated['user_id'] = auth()->id();
        $categories = $validated['categories'];
        unset($validated['categories']);
        $result = null;

        DB::beginTransaction();
        try {
            $result = Product::create($validated);
            // HdrCheckout::create([

            // ])

            $createCategories = [];
            foreach ($categories as $category) {
                $dt = [
                    'product_id' => $result->id,
                    'category_id' => $category['id']
                ];
                $createCategories[] = ProductCategory::create($dt);
            }

            $result->categories = $createCategories;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 503);
        }

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
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'nullable|integer',
            'image' => 'nullable|string',
            'stock' => 'nullable|integer',
            'categories' => 'array|nullable',
            'category.*.id' => 'nullable',
        ]);

        // untuk mendapatkan id user dengan role penjual
        $user = auth()->user();

        // validasi hanya users_id role penjual saja yang bisa update products
        if ($user->role != 'penjual') {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        // mendapatkan user_id yang sesuai yang punya products nya
        $data = Product::where('id', $id)->where("user_id", auth()->id())->first();

        $categories = $validated['categories'];
        unset($validated['categories']);
        $result = null;

        // check apakah products nya ada di database
        if ($data == null) {
            return response()->json([
                'messsage' => 'products not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $result = $data->update($validated);

            ProductCategory::where('product_id', $id)->delete();
            $createCatagories = [];
            foreach ($categories as $category) {
                $dt = [
                    'product_id' => $data->id,
                    'category_id' => $category['id']
                ];

                $createCatagories[] = ProductCategory::create($dt);
            }

            $data->categories = $createCatagories;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 503);
        }

        return response()->json([
            'message' => 'Update your product succesfully'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Product::where('id', $id)->where("user_id", auth()->id())->first();

        ProductCategory::where('product_id', $id)->delete();

        if ($data == null) {
            return response()->json([
                'messsage' => 'product not found',
            ], 404);
        }

        $result = $data->delete();

        if ($result == false) {
            return response()->json([
                'message' => 'product is false',
            ], 404);
        }

        return response()->noContent();

    }
}
