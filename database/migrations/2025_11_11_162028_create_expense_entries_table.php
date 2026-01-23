<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_entries', function (Blueprint $table) {
            $table->id();
            // Kunci asing untuk relasi ke tabel Induk (LPJ)
        $table->foreignId('expense_report_id')->constrained()->onDelete('cascade');
        
        // "debit" (Uang Masuk/Kas Awal) atau "credit" (Pengeluaran)
        $table->enum('type', ['debit', 'credit']); 
        
        $table->string('description'); // "Bensin ke Solo"
        $table->decimal('amount', 15, 2); // Nominal
        $table->string('receipt_image_path')->nullable(); // Path ke foto nota
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_entries');
    }
};
