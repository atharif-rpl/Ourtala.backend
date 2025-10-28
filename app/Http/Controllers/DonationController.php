<?php

namespace App\Http\Controllers;

use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <-- 1. Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    /**
     * Menampilkan semua donasi
     */
    public function index()
    {
        // Kode ini sudah benar, menggunakan Resource
        $donations = Donation::latest()->get();
        return DonationResource::collection($donations);
    }

    /**
     * Menyimpan donasi baru (Sudah bisa handle file dan ada logging)
     */
    public function store(Request $request)
    {
        Log::info('Store method entered.'); // <-- Log Awal

        try {
            // 1. Validasi
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'location' => 'nullable|string',
                'description' => 'nullable|string',
                'target_amount' => 'required|numeric|min:0',
                'amount_collected' => 'nullable|numeric|min:0',
                'whatsapp_link' => 'nullable|url',
                'status' => ['required', Rule::in(['active', 'completed', 'closed'])],
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
            Log::info('Validation passed.'); // Log setelah validasi

            // 2. Handle File Upload jika ada
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('donations', 'public');
                Log::info('Image uploaded to: ' . $imagePath); // Log setelah upload
            }

            // 3. Buat data di database
            $donation = Donation::create([
                'title' => $validatedData['title'],
                'location' => $validatedData['location'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'target_amount' => $validatedData['target_amount'],
                'amount_collected' => $validatedData['amount_collected'] ?? 0,
                'whatsapp_link' => $validatedData['whatsapp_link'] ?? null,
                'status' => $validatedData['status'],
                'image' => $imagePath,
            ]);
            Log::info('Donation created with ID: ' . $donation->id); // Log setelah create

            Log::info('Returning DonationResource from store.'); // <-- Log Akhir (Sukses)
            // Kirim balasan sukses menggunakan Resource
            return (new DonationResource($donation))
                    ->response()
                    ->setStatusCode(201);

        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage(), ['exception' => $e]); // <-- Log Error
            // Kirim balasan error jika terjadi exception
            return response()->json([
                'message' => 'Internal Server Error while creating donation.',
                'error' => $e->getMessage() // Kirim pesan error (opsional, hati-hati di produksi)
            ], 500);
        }
    }

    /**
     * Menampilkan 1 data donasi (untuk form edit)
     */
    public function show(Donation $donation)
    {
        // Kode ini sudah benar, menggunakan Resource
        return new DonationResource($donation);
    }

    /**
     * Mengupdate data donasi (Sudah bisa handle file dan ada logging)
     */
    public function update(Request $request, Donation $donation)
    {
        Log::info('Update method entered for ID: ' . $donation->id); // <-- Log Awal

        try {
            // 1. Validasi
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'location' => 'nullable|string',
                'description' => 'nullable|string',
                'target_amount' => 'required|numeric|min:0',
                'amount_collected' => 'nullable|numeric|min:0',
                'whatsapp_link' => 'nullable|url',
                'status' => ['required', Rule::in(['active', 'completed', 'closed'])],
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
            Log::info('Validation passed for update.'); // Log setelah validasi

            // 2. Handle File Upload (jika ada file baru)
            $imagePath = $donation->image;
            if ($request->hasFile('image')) {
                if ($donation->image) {
                    Storage::disk('public')->delete($donation->image);
                    Log::info('Old image deleted: ' . $donation->image); // Log hapus gambar lama
                }
                $imagePath = $request->file('image')->store('donations', 'public');
                Log::info('Image updated to: ' . $imagePath); // Log setelah update gambar
            }

            // 3. Update data di database
            $donation->update([
                'title' => $validatedData['title'],
                'location' => $validatedData['location'] ?? $donation->location,
                'description' => $validatedData['description'] ?? $donation->description,
                'target_amount' => $validatedData['target_amount'],
                'amount_collected' => $validatedData['amount_collected'] ?? $donation->amount_collected,
                'whatsapp_link' => $validatedData['whatsapp_link'] ?? $donation->whatsapp_link,
                'status' => $validatedData['status'],
                'image' => $imagePath,
            ]);
            Log::info('Donation updated for ID: ' . $donation->id); // Log setelah update

            Log::info('Returning DonationResource from update.'); // <-- Log Akhir (Sukses)
            // Kirim balasan sukses menggunakan Resource
            return new DonationResource($donation);

        } catch (\Exception $e) {
            Log::error('Error in update method: ' . $e->getMessage(), ['exception' => $e]); // <-- Log Error
            // Kirim balasan error jika terjadi exception
            return response()->json([
                'message' => 'Internal Server Error while updating donation.',
                'error' => $e->getMessage() // Kirim pesan error (opsional)
            ], 500);
        }
    }

    /**
     * Menghapus donasi
     */
    public function destroy(Donation $donation)
    {
        Log::info('Destroy method entered for ID: ' . $donation->id); // Log awal

        try {
            // Hapus gambar dari storage
            if ($donation->image) {
                Storage::disk('public')->delete($donation->image);
                Log::info('Image deleted from storage: ' . $donation->image); // Log hapus gambar
            }

            // Hapus data dari database
            $donation->delete();
            Log::info('Donation deleted from database for ID: ' . $donation->id); // Log hapus DB

            Log::info('Returning success response from destroy.'); // Log akhir
            return response()->json([
                'message' => 'Donasi berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in destroy method: ' . $e->getMessage(), ['exception' => $e]); // Log error
            return response()->json([
                'message' => 'Internal Server Error while deleting donation.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}