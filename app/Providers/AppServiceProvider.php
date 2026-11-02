<?php

namespace App\Providers;

use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Format uang Indonesia: @angka(1250000) → 1.250.000, @rupiah(1250000) → Rp 1.250.000, @rupiah(-5000) → -Rp 5.000
        // Jumlah permintaan lupa password yang menunggu, untuk badge notifikasi admin di navbar
        View::composer('layouts.navigation', function ($view) {
            $user = auth()->user();
            $view->with('pendingResetRequests', $user?->isAdmin() ? PasswordResetRequest::pending()->count() : 0);
        });

        Blade::directive('angka', fn (string $value) => "<?php echo number_format((float) ($value), 0, ',', '.'); ?>");
        // Minus ditulis di depan: -Rp 600.000 (sama seperti di PDF)
        Blade::directive('rupiah', fn (string $value) => "<?php echo ((float) ($value) < 0 ? '-' : '').'Rp '.number_format(abs((float) ($value)), 0, ',', '.'); ?>");
    }
}
