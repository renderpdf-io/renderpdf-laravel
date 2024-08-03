<?php

namespace RenderPdfIo;

use Illuminate\Support\Facades\Facade;
use RenderPdfIo\Services\RenderPdfIoService;
use RenderPdfIo\Services\RenderPdfOptions;

/**
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
