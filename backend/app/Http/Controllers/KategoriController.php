<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\KategoriResource;

class KategoriController extends Controller
{
    /**
     * GET /api/kategoris
     */
    public function index()
    {
        // Mengambil semua kategori
        $kategoris = Kategori::all();
        return KategoriResource::collection($kategoris);
    }

    /**
     * POST /api/kategoris
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Validasi: Wajib isi, string, maksimal 255, dan harus unik di tabel kategoris
            'namaKategori' => 'required|string|max:255|unique:kategoris,namaKategori',
        ]);

        $kategori = Kategori::create($data);

        return response()->json(new KategoriResource($kategori), Response::HTTP_CREATED);
    }

    /**
     * GET /api/kategoris/{id}
     */
    public function show(Kategori $kategori)
    {
        return new KategoriResource($kategori);
    }

    /**
     * PUT /api/kategoris/{id}
     */
    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            // Validasi unik dikecualikan untuk ID kategori yg sedang diedit
            'namaKategori' => 'required|string|max:255|unique:kategoris,namaKategori,'.$kategori->id,
        ]);

        $kategori->update($data);

        return new KategoriResource($kategori);
    }

    /**
     * DELETE /api/kategoris/{id}
     */
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}