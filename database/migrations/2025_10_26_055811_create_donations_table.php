<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Kita ganti dari 'return new class' menjadi class yang punya nama
class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Hanya menyimpan path/URL gambar

            // Gunakan bigInteger untuk uang agar bisa menampung nilai besar
            $table->bigInteger('amount_collected')->default(0);
            $table->bigInteger('target_amount');

            $table->string('whatsapp_link')->nullable();

            // 'active' atau 'inactive'
            $table->string('status')->default('active');

            $table->timestamps(); // membuat `created_at` dan `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}