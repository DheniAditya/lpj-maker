<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Format uang Indonesia: @angka(1250000) → 1.250.000, @rupiah(1250000) → Rp 1.250.000
        Blade::directive('angka', fn (string $value) => "<?php echo number_format((float) ($value), 0, ',', '.'); ?>");
        Blade::directive('rupiah', fn (string $value) => "<?php echo 'Rp '.number_format((float) ($value), 0, ',', '.'); ?>");
    }
}
