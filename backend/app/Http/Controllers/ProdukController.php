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
        // Inisialisasi query builder
        $query = Produk::with(['penjual', 'kategori']);

        // 1. Logic untuk Penjual (hanya tampilkan produk mereka)
        if ($request->user() && method_exists($request->user(), 'produks')) {
            // Jika ada user yang login (penjual), filter produk berdasarkan penjual
            $query->where('penjual_id', $request->user()->id);
        }

        // 2. Logic Filtering Kategori (berlaku untuk Publik & Penjual)
        if ($request->has('kategori')) {
            $categoryName = $request->query('kategori');

            //  whereHas untuk memfilter produk berdasarkan nama kategori
            $query->whereHas('kategori', function ($q) use ($categoryName) {
                // Pastikan sesuai dengan nama kolom di tabel kategoris
                $q->whereRaw('LOWER("namaKategori") = ?', [strtolower($categoryName)]);
            });
        }

        // Ambil hasil produk
        $produk = $query->get();

        if($produk->count() > 0){
            return ProdukResource::collection($produk);
        } else {
            return response()->json(['data' => [], 'message' => 'No record available'], 200);
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
            'kondisi' => 'required|in:Baru,Bekas',
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
        $produk->load(['penjual', 'kategori', 'reviews']);
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
        'kondisi' => 'sometimes|required|in:Baru,Bekas',
        ]);

        $produk->update($data);

        return response()->json($produk);
    }

    /**
     * Logic untuk Pencarian Produk berdasarkan nama atau deskripsi.
     * Rute yang digunakan: /api/products/search?q=keyword
     */
    public function search(Request $request)
    {
        // Ambil query pencarian dari parameter 'q'
        $searchQuery = $request->input('q');

        // Pastikan query tidak kosong atau terlalu pendek
        if (empty($searchQuery) || strlen($searchQuery) < 1) { 
            return response()->json([
                'data' => [], 
                'message' => 'Query pencarian terlalu pendek.'
            ], 200);
        }

        // Inisialisasi query builder
        $query = Produk::with(['penjual', 'kategori']);

        // Filter: Cari produk yang namaProduk atau deskripsi mengandung kata kunci
        // Menggunakan LIKE dan % untuk pencarian parsial (partial search)
        $query->where(function ($q) use ($searchQuery) {
            $q->where('namaProduk', 'LIKE', '%' . $searchQuery . '%')
              ->orWhere('deskripsi', 'LIKE', '%' . $searchQuery . '%');
        });

        // Ambil hasil produk
        $produk = $query->get();

        if ($produk->count() > 0) {
            // Menggunakan ProdukResource untuk format output yang konsisten
            return ProdukResource::collection($produk);
        } else {
            return response()->json([
                'data' => [], 
                'message' => 'Produk tidak ditemukan.'
            ], 200);
        }
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


