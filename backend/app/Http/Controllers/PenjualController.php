<?php

namespace App\Http\Controllers;

use App\Models\Penjual;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'nik' => ['required', 'string', 'max:50', 'unique:penjuals,nik'],
            'email' => ['required', 'email', 'max:255', 'unique:penjuals,email'],
            'namaToko' => ['required', 'string', 'max:255'],
            'deskripsiToko' => ['nullable', 'string'],
            'namaPenjual' => ['required', 'string', 'max:255'],
            'noHp' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'string'],
            'fotoKtp' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive,pending'],
            'password' => ['required', 'string', 'min:6'],
            'alamat_id' => ['nullable', 'exists:alamats,id'],
        ]);

        $penjual = Penjual::create($data);

        return response()->json($penjual->load(['alamat', 'produks']), 201);
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
}