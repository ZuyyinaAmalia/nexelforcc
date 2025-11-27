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
    public function index(Request $request)
    {
        // Jika ada user yang login (penjual), filter produk berdasarkan penjual
        if ($request->user() && method_exists($request->user(), 'produks')) {
            $produk = $request->user()->produks()->with('kategori')->get();
        } else {
            // Jika public atau admin, tampilkan semua produk
            $produk = Produk::with('kategori')->get();
        }

        if($produk->count() > 0){
            return ProdukResource::collection($produk);
        } else {
            return response()->json(['message' => 'No record available'], 200);
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

        // Auto-assign penjual_id jika user adalah penjual
        if ($request->user() && method_exists($request->user(), 'produks')) {
            $data['penjual_id'] = $request->user()->id;
        }

        $produk = Produk::create($data);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data' => new ProdukResource($produk)
        ], Response::HTTP_CREATED);
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
