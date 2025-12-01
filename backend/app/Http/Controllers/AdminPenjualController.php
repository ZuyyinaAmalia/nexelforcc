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
     * 1. Ambil daftar penjual yang statusnya PENDING (DENGAN RELASI ALAMAT)
     */
    public function index()
    {
        try {
            // PENTING: Load relasi 'alamat' agar data alamat ikut terkirim
            $penjuals = \App\Models\Penjual::with('alamat')// 👈 TAMBAHKAN INI
                                ->where('status', 'PENDING')
                                ->orderBy('created_at', 'desc')
                                ->get();
            
            // Debug: Log jumlah data yang ditemukan
            \Log::info('Jumlah penjual pending: ' . $penjuals->count());
            
            return response()->json($penjuals);

        } catch (\Exception $e) {
            // KALAU ERROR, TAMPILKAN PESANNYA LANGSUNG
            \Log::error('Error di AdminPenjualController@index: ' . $e->getMessage());
            
            return response()->json([
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * 2. Proses Verifikasi (Terima / Tolak)
     */
    public function verifikasi(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:ACTIVE,REJECTED',
                'alasan' => 'nullable|string' // Validasi input alasan
            ]);

            $penjual = Penjual::findOrFail($id);
            
            // Update Status
            $penjual->status = $request->status;

            // Jika DITOLAK, simpan alasannya
            if ($request->status === 'REJECTED') {
                $penjual->alasan_ditolak = $request->alasan;
            } else {
                // Jika diterima, hapus alasan lama (opsional, biar bersih)
                $penjual->alasan_ditolak = null;
            }

            $penjual->save();

            // Kirim Email
            try {
                if ($request->status === 'ACTIVE') {
                    Mail::to($penjual->email)->send(new PenjualDiterima($penjual));
                    \Log::info("Email diterima berhasil dikirim ke: {$penjual->email}");
                } else {
                    // Class PenjualDitolak otomatis akan baca $penjual->alasan_ditolak
                    Mail::to($penjual->email)->send(new PenjualDitolak($penjual));
                    \Log::info("Email ditolak berhasil dikirim ke: {$penjual->email}");
                }
            } catch (\Exception $e) {
                // Log error jika email gagal, tapi jangan hentikan proses
                \Log::error("Email error untuk penjual ID {$id}: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => $penjual->load('alamat') // Load alamat juga
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error verifikasi penjual ID {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}