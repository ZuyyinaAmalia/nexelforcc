<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjual;
use App\Models\Review;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard/stats
     * Mengambil semua statistik untuk dashboard admin
     */
    public function getStatistics()
    {
        return response()->json([
            'produk_per_kategori' => $this->getProdukPerKategori(),
            'penjual_per_provinsi' => $this->getPenjualPerProvinsi(),
            'user_penjual_status' => $this->getUserPenjualStatus(),
            'pengunjung_review_stats' => $this->getPengunjungReviewStats(),
        ]);
    }

    /**
     * Statistik 1: Jumlah Produk per Kategori
     */
    private function getProdukPerKategori()
    {
        return Kategori::withCount('produks')
            ->get()
            ->map(function ($kategori) {
                return [
                    'kategori' => $kategori->namaKategori,
                    'jumlah' => $kategori->produks_count
                ];
            });
    }

    /**
     * Statistik 2: Jumlah Toko per Provinsi
     * Catatan: Karena penjual punya relasi ke alamat, 
     * kita ambil dari tabel alamats yang punya kolom provinsi
     */
    private function getPenjualPerProvinsi()
    {
        // Jika Anda punya tabel alamats dengan kolom provinsi
        return DB::table('penjuals')
            ->join('alamats', 'penjuals.id', '=', 'alamats.penjual_id')
            ->select('alamats.provinsi', DB::raw('COUNT(DISTINCT penjuals.id) as jumlah'))
            ->where('penjuals.status', 'ACTIVE') // Hanya hitung toko aktif
            ->groupBy('alamats.provinsi')
            ->orderBy('jumlah', 'desc')
            ->get();
        
        // Jika TIDAK punya kolom provinsi di alamats, 
        // uncomment kode di bawah untuk simulasi data sementara:
        /*
        return collect([
            ['provinsi' => 'DKI Jakarta', 'jumlah' => 45],
            ['provinsi' => 'Jawa Barat', 'jumlah' => 32],
            ['provinsi' => 'Jawa Timur', 'jumlah' => 28],
            ['provinsi' => 'Bali', 'jumlah' => 15],
            ['provinsi' => 'Sumatera Utara', 'jumlah' => 12],
        ]);
        */
    }

    /**
     * Statistik 3: User Penjual Aktif vs Tidak Aktif
     */
    private function getUserPenjualStatus()
    {
        $aktif = Penjual::where('status', 'ACTIVE')->count();
        $tidakAktif = Penjual::whereIn('status', ['PENDING', 'INACTIVE', 'REJECTED'])->count();
        
        return [
            'aktif' => $aktif,
            'tidak_aktif' => $tidakAktif,
            'total' => $aktif + $tidakAktif
        ];
    }

    /**
     * Statistik 4: Pengunjung dengan Komentar & Rating
     */
    private function getPengunjungReviewStats()
    {
        $totalReviews = Review::count();
        $uniqueVisitors = Review::distinct('emailPengunjung')->count('emailPengunjung');
        $averageRating = Review::avg('rating');
        
        // Distribusi rating
        $ratingDistribution = Review::select('rating', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'rating' => $item->rating,
                    'jumlah' => $item->jumlah
                ];
            });

        return [
            'total_reviews' => $totalReviews,
            'unique_visitors' => $uniqueVisitors,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $ratingDistribution
        ];
    }

    /**
     * Bonus: Top 5 Produk dengan Rating Tertinggi
     */
    public function getTopProducts()
    {
        return Produk::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->having('reviews_count', '>', 0)
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(5)
            ->get()
            ->map(function ($produk) {
                return [
                    'nama_produk' => $produk->namaProduk,
                    'rating' => round($produk->reviews_avg_rating, 2),
                    'jumlah_review' => $produk->reviews_count
                ];
            });
    }
    // pdf report methods
    public function downloadReportStatus()
    {
        // Ambil data detail penjual
        $penjuals = Penjual::select('namaPenjual', 'namaToko', 'email', 'status', 'created_at')
            ->orderBy('status', 'asc')
            ->get();

        $pdf = Pdf::loadView('reports.penjual_status', [
            'data' => $penjuals,
            'date' => date('d-m-Y H:i')
        ]);

        return $pdf->download('laporan_status_penjual.pdf');
    }

    /**
     * LAPORAN 2: Daftar Penjual per Provinsi
     */
    public function downloadReportProvinsi()
    {
        // Join ke tabel alamat untuk dapat provinsi
        $penjuals = DB::table('penjuals')
            ->join('alamats', 'penjuals.id', '=', 'alamats.penjual_id')
            ->select('penjuals.namaToko', 'penjuals.namaPenjual', 'alamats.provinsi', 'alamats.kota', 'penjuals.status')
            ->orderBy('alamats.provinsi', 'asc')
            ->get()
            ->groupBy('provinsi'); // Grouping biar rapi di PDF

        $pdf = Pdf::loadView('reports.penjual_provinsi', [
            'data' => $penjuals,
            'date' => date('d-m-Y H:i')
        ]);

        return $pdf->download('laporan_penjual_per_provinsi.pdf');
    }
    /**
     * LAPORAN 3: Daftar Produk & Rating (Sorted by Rating Desc)
     * Sesuai SRS-MartPlace-11
     */
    public function downloadReportProdukRating()
    {
        // Ambil data produk dengan relasi yang dibutuhkan
        $products = Produk::with(['penjual.alamat', 'kategori'])
            ->withAvg('reviews', 'rating') // Hitung rata-rata rating
            ->orderByDesc('reviews_avg_rating') // Urutkan dari rating tertinggi
            ->get();

        $pdf = Pdf::loadView('reports.produk_rating', [
            'data' => $products,
            'date' => date('d-m-Y H:i')
        ]);

        return $pdf->download('laporan_produk_rating.pdf');
    }
}