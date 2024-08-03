<?php

namespace RenderPdfIo;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use RenderPdfIo\Services\RenderPdfIoService;

class RenderPdfIoServiceProvider extends ServiceProvider
{
    const VERSION = 'v1.0.0';

    public function boot(): void
    {
        AboutCommand::add(
            'RenderPDF.io: Laravel Library',
            fn () => ['Version' => self::VERSION]
        );
    }

    public function register(): void
    {
        $this->app->singleton(RenderPdfIoService::class);
    }
}
