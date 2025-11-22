<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ProdukResource;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::get();

        if($produk->count() > 0){
            return ProdukResource::collection($produk);
        } else {
            return response()->json(['message' => 'No record available', 200]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'namaProduk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'fotoProduk' => 'nullable|string',
            'kategori_id' => 'nullable|integer',
        ]);

        $produk = Produk::create($data);

        return response()->json(new ProdukResource($produk), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        return response()->json($produk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'namaProduk' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'sometimes|required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'fotoProduk' => 'nullable|string',
            'kategori_id' => 'nullable|integer',
        ]);

        $produk->update($data);

        return response()->json($produk);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
