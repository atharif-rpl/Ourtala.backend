<?php

namespace App\Http\Controllers;

use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini
use Illuminate\Validation\Rule; // <-- Tambahkan ini

class DonationController extends Controller
{
    /**
     * Menampilkan semua donasi
     */
    public function index()
    {
        $donations = Donation::latest()->get();

        // Gunakan Resource untuk "menerjemahkan" JSON
        // Ini akan mengubah amount_collected -> amountCollected
        return DonationResource::collection($donations);
    }
    /**
     * Menyimpan donasi baru (Sudah bisa handle file)
     */
    public function store(Request $request)
    {
        // 1. Validasi (termasuk file)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'amount_collected' => 'nullable|numeric|min:0',
            'whatsapp_link' => 'nullable|url',
            'status' => ['required', Rule::in(['active', 'completed', 'closed'])],
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validasi file
        ]);

        // 2. Handle File Upload jika ada
        if ($request->hasFile('image')) {
            // Simpan file ke 'public/donations'
            // 'donations' akan dibuat otomatis
            $path = $request->file('image')->store('donations', 'public');
            // 'path' akan berisi 'donations/namagambar.jpg'
            $validatedData['image'] = $path;
        }

        // 3. Buat data di database
        // Kita gunakan nama kolom snake_case dari $validatedData
        $donation = Donation::create([
            'title' => $validatedData['title'],
            'location' => $validatedData['location'],
            'description' => $validatedData['description'],
            'target_amount' => $validatedData['target_amount'],
            'amount_collected' => $validatedData['amount_collected'] ?? 0,
            'whatsapp_link' => $validatedData['whatsapp_link'],
            'status' => $validatedData['status'],
            'image' => $validatedData['image'] ?? null, // Simpan path gambar
        ]);

        return response()->json([
            'message' => 'Donasi berhasil dibuat',
            'data' => $donation
        ], 201);
    }

    /**
     * Menampilkan 1 data donasi (untuk form edit)
     */
    public function show(Donation $donation)
    {
        return response()->json($donation);
    }

    /**
     * Mengupdate data donasi (Sudah bisa handle file)
     */
    public function update(Request $request, Donation $donation)
    {
        // 1. Validasi
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'amount_collected' => 'nullable|numeric|min:0',
            'whatsapp_link' => 'nullable|url',
            'status' => ['required', Rule::in(['active', 'completed', 'closed'])],
            // 'image' tidak 'required' saat update, tapi jika ada, harus valid
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 2. Handle File Upload (jika ada file baru)
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($donation->image) {
                Storage::disk('public')->delete($donation->image);
            }

            // Simpan gambar baru
            $path = $request->file('image')->store('donations', 'public');
            $validatedData['image'] = $path;
        }

        // 3. Update data di database
        $donation->update([
            'title' => $validatedData['title'],
            'location' => $validatedData['location'],
            'description' => $validatedData['description'],
            'target_amount' => $validatedData['target_amount'],
            'amount_collected' => $validatedData['amount_collected'] ?? $donation->amount_collected,
            'whatsapp_link' => $validatedData['whatsapp_link'],
            'status' => $validatedData['status'],
            // Jika ada gambar baru, update, jika tidak, biarkan gambar lama
            'image' => $validatedData['image'] ?? $donation->image,
        ]);

        return response()->json([
            'message' => 'Donasi berhasil diupdate',
            'data' => $donation
        ]);
    }

    /**
     * Menghapus donasi
     */
    public function destroy(Donation $donation)
    {
        // Hapus gambar dari storage
        if ($donation->image) {
            Storage::disk('public')->delete($donation->image);
        }

        // Hapus data dari database
        $donation->delete();

        return response()->json([
            'message' => 'Donasi berhasil dihapus'
        ], 200);
    }
}