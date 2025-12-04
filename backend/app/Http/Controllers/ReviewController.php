<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewTerima;

class ReviewController extends Controller
{
    /**
     * READ (All): Menampilkan semua review (Bisa untuk Admin)
     * Atau filter berdasarkan ?product_id=xxx
     */
    public function index(Request $request)
    {
        // Jika ada parameter 'product_id' di URL, filter datanya
        // Contoh: GET /api/reviews?product_id=123
        if ($request->has('product_id')) {
            $reviews = Review::where('produk_id', $request->product_id)
                            ->latest()
                            ->get();
        } else {
            // Jika tidak ada filter, ambil semua (misal untuk dashboard admin)
            $reviews = Review::latest()->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    /**
     * READ (By Product ID): Sesuai Diagram Class
     * GET /api/reviews/product/{id}
     */
    public function getReviewByIdProduk($produkId)
    {
        $reviews = Review::where('produk_id', $produkId)
                        ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                        ->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Belum ada review untuk produk ini',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    /**
     * CREATE: Menambah Review Baru
     * POST /api/reviews
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = \Validator::make($request->all(), [
            'produk_id' => 'required|exists:produks,id', // Pastikan tabel produk ada
            'rating'    => 'required|integer|min:1|max:5',
            'ulasan'    => 'required|string',
            'namaPengunjung' => 'required|string',
            'noHpPengunjung' => 'required|string',
            
            // Validasi Provinsi (Harus sesuai Enum di Model)
            'provinsiPengunjung' => ['required', Rule::in(Review::PROVINSI)],

            // Validasi Unik: 1 Email hanya boleh 1 review per produk
            'emailPengunjung' => [
                'required', 'email',
                Rule::unique('reviews')->where(function ($query) use ($request) {
                    return $query->where('produk_id', $request->produk_id);
                })
            ],
        ], [
            'emailPengunjung.unique' => 'Anda sudah memberikan review untuk produk ini sebelumnya.',
            'provinsiPengunjung.in' => 'Nama provinsi tidak valid.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Simpan Data
        $data = $request->except(['id']);
        $review = Review::create($data);

        //  KIRIM EMAIL UCAPAN TERIMA KASIH
        try {
            // Ambil detail produk
            $produk = Produk::find($request->produk_id);
            
            if ($produk) {
                // Kirim email
                Mail::to($request->emailPengunjung)->send(
                    new ReviewTerima(
                        $request->namaPengunjung,
                        $produk->namaProduk,
                        $request->rating,
                        $request->ulasan,
                        $request->noHpPengunjung,
                        $request->provinsiPengunjung
                    )
                );
                
                // Log sukses
                \Log::info('Email review terima kasih berhasil dikirim ke: ' . $request->emailPengunjung);
            }
        } catch (\Exception $e) {
            // Jangan batalkan review jika email gagal
            \Log::error('Error sending review email: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil ditambahkan',
            'data' => $review
        ], 201);
    }

    /**
     * READ (Detail): Melihat 1 Review spesifik
     * GET /api/reviews/{id}
     */
    public function show($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review tidak ditemukan'], 404);
        }

        return response()->json(['data' => $review]);
    }

    /**
     * UPDATE: Mengedit Review (Misal oleh Admin atau User ybs)
     * PUT /api/reviews/{id}
     */
    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review tidak ditemukan'], 404);
        }

        // Validasi (Sedikit berbeda dengan store, tidak perlu cek unique ketat)
        $validator = \Validator::make($request->all(), [
            'rating'    => 'integer|min:1|max:5',
            'ulasan'    => 'string',
            'namaPengunjung' => 'string',
            'noHpPengunjung' => 'string',
            'provinsiPengunjung' => [Rule::in(Review::PROVINSI)],
            // Saat update, email boleh sama dengan dirinya sendiri (ignore current id)
            'emailPengunjung' => [
                'email',
                Rule::unique('reviews')->where(function ($query) use ($review) {
                    return $query->where('produk_id', $review->produk_id);
                })->ignore($review->id)
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update hanya field yang dikirim saja
        $review->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil diperbarui',
            'data' => $review
        ]);
    }

    /**
     * DELETE: Menghapus Review
     * DELETE /api/reviews/{id}
     */
    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review tidak ditemukan'], 404);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil dihapus'
        ]);
    }
}