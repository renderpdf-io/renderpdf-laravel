<?php

namespace RenderPdfIo;

use Illuminate\Support\Facades\Facade;
use RenderPdfIo\Services\RenderPdfIoService;
use RenderPdfIo\Services\RenderPdfOptions;

/**
 * Welcome to RenderPDF.io, convert HTML to modern PDF files in seconds
 * Get your API Key at: https://renderpdf.io (we offer high usage for FREE plan - highest on the market)
 *
 * @method static string render(RenderPdfOptions $options)
 * @method static string renderAsync(RenderPdfOptions $options)
 *
 * @mixin RenderPdfIoService
 */
class RenderPdfIo extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return RenderPdfIoService::class;
    }
}
