<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** File tanda tangan (PNG transparan) untuk dicetak di PDF LPJ. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('expense_reports', 'signature_path')) {
            Schema::table('expense_reports', function (Blueprint $table) {
                $table->string('signature_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('expense_reports', 'signature_path')) {
            Schema::table('expense_reports', function (Blueprint $table) {
                $table->dropColumn('signature_path');
            });
        }
    }
};
