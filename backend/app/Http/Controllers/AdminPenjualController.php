<?php

namespace App\Http\Controllers;

use App\Models\Penjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PenjualDiterima;
use App\Mail\PenjualDitolak;

class AdminPenjualController extends Controller
{
    /**
     * 1. Ambil daftar penjual yang statusnya PENDING
     */
    public function index()
    {
        try {
            // Kita coba ambil data sederhana dulu
            $penjuals = \App\Models\Penjual::where('status', 'PENDING')
                                ->orderBy('created_at', 'desc')
                                ->get();
            
            return response()->json($penjuals);

        } catch (\Exception $e) {
            // KALAU ERROR, TAMPILKAN PESANNYA LANGSUNG
            return response()->json([
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * 2. Proses Verifikasi (Terima / Tolak)
     */
    public function verifikasi(Request $request, $id)
    {
        // Validasi Input: Status harus ACTIVE atau REJECTED (Huruf Besar)
        $request->validate([
            'status' => 'required|in:ACTIVE,REJECTED' 
        ]);

        // Cari Penjual, jika tidak ketemu akan otomatis 404
        $penjual = Penjual::findOrFail($id);
        
        // Update Status di Database
        $penjual->status = $request->status;
        $penjual->save();

        // Logika Kirim Email Notifikasi
        // Kita bungkus try-catch agar jika internet mati/mailtrap error,
        // proses update status TIDAK ikut gagal.
        try {
            if ($request->status === 'ACTIVE') {
                Mail::to($penjual->email)->send(new PenjualDiterima($penjual));
            } else {
                Mail::to($penjual->email)->send(new PenjualDitolak($penjual));
            }
        } catch (\Exception $e) {
            // Email gagal? Tidak masalah, biarkan coding lanjut jalan.
            // Anda bisa uncomment baris bawah untuk debugging log:
            // \Log::error("Email Error: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Verifikasi berhasil. Status penjual sekarang: ' . $request->status,
            'data' => $penjual
        ]);
    }
}