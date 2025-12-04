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
            $query->where('penjual_id', $request->user()->id);
        }

        // 2. Logic Filtering Kategori (berlaku untuk Publik & Penjual)
        if ($request->has('kategori')) {
            $categoryName = $request->query('kategori');

            $query->whereHas('kategori', function ($q) use ($categoryName) {
                // Kolom 'namaKategori' diapit kutip ganda untuk PostgreSQL (Case-Sensitive)
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
        $searchQuery = $request->input('q');
        $keyword = '%' . strtolower($searchQuery) . '%';
        $perPage = (int) $request->query('per_page', 15);

        if (empty($searchQuery) || strlen($searchQuery) < 1) { 
            return response()->json([
                'data' => [], 
                'message' => 'Query pencarian terlalu pendek.'
            ], 200);
        }

        // 2. Inisialisasi Query Builder dengan Join
        $query = Produk::query()
            ->join('penjuals', 'produks.penjual_id', '=', 'penjuals.id')
            ->join('alamats', 'penjuals.id', '=', 'alamats.penjual_id')
            ->join('kategoris', 'produks.kategori_id', '=', 'kategoris.id');

        // 3. Logika Pencarian Gabungan (WHERE)
        $query->where(function ($q) use ($keyword) {
            
            // FIX PENTING: Tambahkan kutip ganda pada nama kolom CamelCase di PostgreSQL
            $q->whereRaw('LOWER(produks."namaProduk") LIKE ?', [$keyword]) // <--- PERUBAHAN DI SINI
            ->orWhereRaw('LOWER(produks.deskripsi) LIKE ?', [$keyword])
            
            // FIX PENTING: Tambahkan kutip ganda pada nama kolom CamelCase 'namaToko'
            ->orWhereRaw('LOWER(penjuals."namaToko") LIKE ?', [$keyword]) // <--- PERUBAHAN DI SINI
            
            // 'namaKategori' sudah benar diperbaiki di langkah sebelumnya
            ->orWhereRaw('LOWER(kategoris."namaKategori") LIKE ?', [$keyword])
            
            // Kolom snake_case/lowercase seperti 'kota' dan 'provinsi' tidak butuh kutip ganda
            ->orWhereRaw('LOWER(alamats.kota) LIKE ?', [$keyword])
            ->orWhereRaw('LOWER(alamats.provinsi) LIKE ?', [$keyword]);
        });
        
        // 4. Ambil Hasil
        $produks = $query
            ->select('produks.*') 
            ->with(['penjual.alamat', 'kategori']) 
            ->latest() 
            ->paginate($perPage);

        // 5. Response
        if ($produks->count() > 0) {
            return response()->json($produks, 200);
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


