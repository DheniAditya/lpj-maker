<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom fitur "Bagikan Link".
 *
 * Sebelumnya migration ini kosong sehingga instalasi baru (php artisan migrate)
 * tidak punya kolom sharing. Setiap kolom dicek dulu supaya aman dijalankan
 * di database yang kolomnya sudah dibuat manual.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('expense_reports', 'is_shared')) {
                $table->boolean('is_shared')->default(false);
            }
            if (! Schema::hasColumn('expense_reports', 'share_role')) {
                $table->string('share_role', 20)->default('viewer');
            }
            if (! Schema::hasColumn('expense_reports', 'share_token')) {
                $table->string('share_token', 64)->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expense_reports', function (Blueprint $table) {
            $columns = array_filter(
                ['is_shared', 'share_role', 'share_token'],
                fn (string $column) => Schema::hasColumn('expense_reports', $column)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
