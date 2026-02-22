<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('transaction_images', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke tabel expense_entries
        $table->foreignId('expense_entry_id')
              ->constrained('expense_entries')
              ->onDelete('cascade'); // Jika transaksi dihapus, gambar ikut terhapus
        $table->string('image_path');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_images');
    }
};
