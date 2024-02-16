<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::select('id', 'name');

        $category = $category->get();

        return response()->json([
            'data' => $category,
        ]);
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
        // buat category untuk product
        $validated = $request->validate([
            "name" => "required|string",
        ]);

        // untuk mendapatkan id users dengan role penjual
        $user = auth()->user();

        // validasi hanya users_id yang role penjual saja yang bisa bikin category
        if ($user->role != "penjual") {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        Category::create($validated);

        return response()->json([
            'message' => 'your create category succesfully'
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
            'name' => 'required|string'
        ]);

        $user = auth()->user();

        if ($user->role != 'penjual') {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        $data = Category::find( $id );

        if($data == null) {
            return response()->json([
                'messsage' => 'category not found',
            ], 404);
        }

        $data->update($validated);

        return response()->json([
            'message' => 'update category is successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        if ($user->role != "penjual") {
            return response()->json([
                'message' => 'You are not a seller',
            ], 404);
        }

        $data = Category::find($id);

        if($data == null) {
            return response()->json([
                'messsage' => 'Soory, category not found',
            ], 404);
        }

        $data->delete();

        return response()->noContent();
    }
}
