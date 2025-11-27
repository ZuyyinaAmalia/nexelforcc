<?php

namespace App\Http\Controllers;

use App\Models\Penjual;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class PenjualController extends Controller
{
    /**
     * List penjuals (paginated) with relations.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $penjuals = Penjual::with(['alamat', 'produks'])->paginate($perPage);
        return response()->json($penjuals);
    }

    /**
     * Show single penjual.
     */
    public function show(Penjual $penjual)
    {
        $penjual->load(['alamat', 'produks']);
        return response()->json($penjual);
    }

    /**
     * Create new penjual.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|string|max:50|unique:penjuals,nik',
            'email' => 'required|email|max:255|unique:penjuals,email',
            'namaToko' => 'required|string|max:255',
            'deskripsiToko' => 'nullable|string',
            'namaPenjual' => 'required|string|max:255',
            'noHp' => 'nullable|string|max:30',
            'foto' => 'nullable|string',
            'fotoKtp' => 'nullable|string',
            'status' => 'nullable|in:PENDING,ACTIVE,INACTIVE,REJECTED',
            'password' => 'required|string|min:6',
        ]);

        $penjual = Penjual::create([
            'nik' => $data['nik'],
            'email' => $data['email'],
            'namaToko' => $data['namaToko'],
            'deskripsiToko' => $data['deskripsiToko'] ?? null,
            'namaPenjual' => $data['namaPenjual'],
            'noHp' => $data['noHp'] ?? null,
            'foto' => $data['foto'] ?? null,
            'fotoKtp' => $data['fotoKtp'] ?? null,
            'status' => $data['status'] ?? 'PENDING',
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'Penjual berhasil ditambahkan',
            'data' => $penjual,
        ], 201);
    }

    /**
     * Register Penjual Baru (Public)
     * Menangani Password Strict, Upload File, dan Simpan Alamat
     */
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Data Akun
            'email' => 'required|email|unique:penjuals,email',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],

            // Data Pribadi
            'namaPenjual' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:penjuals,nik',
            'noHp' => 'required|string|max:20',
            
            // Validasi File (Harus Gambar)
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'fotoKtp' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB

            // Data Toko
            'namaToko' => 'required|string|max:255',
            'deskripsiToko' => 'required|string',

            // Data Alamat
            'jalan' => 'required|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'desa' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        // 2. Handle Upload File
        // Menyimpan file ke folder 'storage/app/public/penjuals'
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('penjuals/foto_profil', 'public');
        }

        $ktpPath = null;
        if ($request->hasFile('fotoKtp')) {
            $ktpPath = $request->file('fotoKtp')->store('penjuals/ktp', 'public');
        }

        // 3. Buat Data Penjual
        $penjual = Penjual::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'namaPenjual' => $request->namaPenjual,
            'nik' => $request->nik,
            'noHp' => $request->noHp,
            'foto' => $fotoPath,      // Simpan path file
            'fotoKtp' => $ktpPath,    // Simpan path file
            'namaToko' => $request->namaToko,
            'deskripsiToko' => $request->deskripsiToko,
            'status' => 'PENDING',    // Default status
        ]);

        // 4. Buat Data Alamat (Relasi)
        // Kita asumsikan 'user_id' di tabel alamats merujuk ke id penjual
        Alamat::create([
            'user_id' => $penjual->id, // Sambungkan ID Penjual
            'jalan' => $request->jalan,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'desa' => $request->desa,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
        ]);

        // 5. Return Response
        return response()->json([
            'message' => 'Registrasi berhasil! Silakan tunggu verifikasi admin.',
            'data' => $penjual->load('alamat') // Sertakan data alamat di response
        ], 201);
    }

    
    /**
     * Update existing penjual.
     */
    public function update(Request $request, Penjual $penjual)
    {
        $data = $request->validate([
            'nik' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('penjuals', 'nik')->ignore($penjual->id)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('penjuals', 'email')->ignore($penjual->id)],
            'namaToko' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsiToko' => ['nullable', 'string'],
            'namaPenjual' => ['sometimes', 'required', 'string', 'max:255'],
            'noHp' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'string'],
            'fotoKtp' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,pending'],
            'password' => ['nullable', 'string', 'min:6'],
            'alamat_id' => ['nullable', 'exists:alamats,id'],
        ]);

        // If password is empty string or null, remove to avoid overwriting
        if (array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        $penjual->update($data);

        return response()->json($penjual->fresh()->load(['alamat', 'produks']));
    }

    /**
     * Delete penjual.
     */
    public function destroy(Penjual $penjual)
    {
        $penjual->delete();
        return response()->json(null, 204);
    }

    /**
     * Login penjual.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $penjual = Penjual::where('email', $request->email)->first();

        if (! $penjual || ! Hash::check($request->password, $penjual->password)) {
            return response()->json([
                'message' => 'Email atau Password salah.'
            ], 401);
        }

        // Hapus token lama (opsional, biar login hanya di 1 device)
        // $penjual->tokens()->delete();

        // Buat Token Baru
        $token = $penjual->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $penjual->load('alamat')
        ]);
    }

    /**
     * Logout Penjual
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    /**
     * Get profile penjual yang sedang login
     */
    public function profile(Request $request)
    {
        $penjual = $request->user();
        $penjual->load(['alamat', 'produks']);
        
        return response()->json([
            'success' => true,
            'data' => $penjual
        ]);
    }

    /**
     * Update profile penjual yang sedang login
     */
    public function updateProfile(Request $request)
    {
        $penjual = $request->user();

        $data = $request->validate([
            'namaToko' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsiToko' => ['nullable', 'string'],
            'namaPenjual' => ['sometimes', 'required', 'string', 'max:255'],
            'noHp' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle foto upload if exists
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('penjuals/foto_profil', 'public');
            $data['foto'] = $fotoPath;
        }

        $penjual->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diperbarui',
            'data' => $penjual->fresh()->load('alamat')
        ]);
    }

    /**
     * Get dashboard statistics untuk penjual yang sedang login
     */
    public function dashboardStats(Request $request)
    {
        $penjual = $request->user();

        // Hitung total produk
        $totalProduk = $penjual->produks()->count();

        // Hitung total penjualan (jika ada tabel orders/transaksi)
        // Untuk sementara kita set 0, bisa dikembangkan nanti
        $totalPenjualan = 0;

        // Hitung pesanan baru (jika ada tabel orders)
        // Untuk sementara kita set 0
        $pesananBaru = 0;

        // Aktivitas terbaru (bisa dari log atau riwayat transaksi)
        // Untuk sementara kita buat contoh data
        $aktivitasTerbaru = [];

        // Jika ada produk, tambahkan info produk terbaru
        $produkTerbaru = $penjual->produks()->latest()->take(3)->get();
        foreach ($produkTerbaru as $produk) {
            $aktivitasTerbaru[] = [
                'judul' => 'Produk Ditambahkan',
                'deskripsi' => "Produk '{$produk->namaProduk}' berhasil ditambahkan",
                'waktu' => $produk->created_at->diffForHumans(),
                'type' => 'produk'
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_produk' => $totalProduk,
                'total_penjualan' => $totalPenjualan,
                'pesanan_baru' => $pesananBaru,
                'aktivitas_terbaru' => $aktivitasTerbaru,
                'penjual' => [
                    'nama' => $penjual->namaPenjual,
                    'toko' => $penjual->namaToko,
                    'status' => $penjual->status
                ]
            ]
        ]);
    }
}