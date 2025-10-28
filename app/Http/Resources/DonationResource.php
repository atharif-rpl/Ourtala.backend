<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'this' adalah model 'Donation'
        return [
            // Data yang namanya sudah sama
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            // --- INI BAGIAN PENERJEMAHNYA ---

            // 1. Mengubah path 'image' menjadi URL lengkap
            'imageUrl' => $this->image ? asset('storage/' . $this->image) : null,

            // 2. Menerjemahkan snake_case (database) ke camelCase (frontend)
            'amountCollected' => $this->amount_collected,
            'amountTarget' => $this->target_amount,
            'whatsappLink' => $this->whatsapp_link,
        ];
    }
}