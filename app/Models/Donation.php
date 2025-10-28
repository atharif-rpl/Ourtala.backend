<?php

namespace App\Models; // pastikan namespace ini sesuai dengan struktur project kamu

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    public $timestamps = true;

    // Tetap gunakan snake_case untuk kolom database
    protected $fillable = [
        'title',
        'location',
        'description',
        'image',
        'amount_collected',
        'target_amount',
        'whatsapp_link',
        'status',
    ];

    // --- Accessors (opsional jika frontend ingin camelCase) ---
    public function getAmountCollectedAttribute($value)
    {
        return $value;
    }

    public function getTargetAmountAttribute($value)
    {
        return $value;
    }

    public function getWhatsappLinkAttribute($value)
    {
        return $value;
    }

    // --- Automatic camelCase support for frontend ---
    public function getAttribute($key)
    {
        // Izinkan akses langsung camelCase (misal $donation->amountCollected)
        if (!array_key_exists($key, $this->attributes)) {
            $snakeKey = Str::snake($key);
            if (array_key_exists($snakeKey, $this->attributes)) {
                return parent::getAttribute($snakeKey);
            }
        }
        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        // Otomatis ubah camelCase ke snake_case sebelum disimpan
        return parent::setAttribute(Str::snake($key), $value);
    }
}
