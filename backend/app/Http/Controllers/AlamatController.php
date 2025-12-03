<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Alamat;


class AlamatController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET api/alamats
     */
    public function index()
    {
        //ambil semua data alamat dengan relasi penjual
        try {
            $alamats = Alamat::with('penjual')->get();
    
            return response()->json([
                'success'=> true,
                'message'=>'Data alamat berhasil diambil',
                'data' => $alamats
            ], 200);
            //code...
        } catch (\Exception $e) {
            return response() ->json([
                'success' => false,
                'message' => 'Gagal mengambil data alamat'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/alamats
     */
    public function store(Request $request)
    {
        //validasi input
        $validator = Validator::make($request->all(),[
            'penjual_id' => 'required|exists:penjuals,id',
            'jalan' => 'required|string|max:255',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'desa' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|in:' . implode(',', Alamat::PROVINSI_LIST),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'=>false,
                'message'=>'Validasi gagal',
                'errors'=> $validator->errors()
            ],422);
        }

        try {
            $alamat = Alamat::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'data' => $alamat -> load('penjual')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan alamat: ' . $e->getMessage()
            ], 500);        
        }
    }

    /**
     * Display the specified resource.
     * GET /api/alamats{id}
     */
    public function show(string $id)
    {
        try {
            $alamat = Alamat::with('penjual')->find($id);

            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data alamat berhasil diambil',
                'data' => $alamat
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/alamats/{id}
     */
    public function update(Request $request, string $id)
    {
        try {
            $alamat = Alamat::find($id);

            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'penjual_id' => 'sometimes|exists:penjuals,id',
                'jalan' => 'sometimes|string|max:255',
                'rt' => 'sometimes|string|max:3',
                'rw' => 'sometimes|string|max:3',
                'desa' => 'sometimes|string|max:100',
                'kecamatan' => 'sometimes|string|max:100',
                'kota' => 'sometimes|string|max:100',
                'provinsi' => 'sometimes|in:' . implode(',', Alamat::PROVINSI_LIST),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update data
            $alamat->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diupdate',
                'data' => $alamat->load('penjual')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate alamat: ' . $e->getMessage()
            ], 500);
        }    
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/alamats/{id}
     */
    public function destroy(string $id)
    {
        try {
            $alamat = Alamat::find($id);

            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $alamat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus alamat: ' . $e->getMessage()
            ], 500);
        }    
    }
}
