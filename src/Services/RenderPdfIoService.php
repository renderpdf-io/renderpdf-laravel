<?php

namespace RenderPdfIo\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use LogicException;
use RenderPdfIo\Exceptions\RenderPdfIoException;

class RenderPdfIoService
{
    /**
     * Render a PDF in sync mode
     *
     * @param RenderPdfOptions $option
     *
     * @return string file url
     *
     * @throws RenderPdfIoException on render failure
     * @throws ConnectionException on unable to make the API call (local network error)
     */
    public function render(RenderPdfOptions $option): string
    {
        $res = $this->getHttpClient()
            ->post('render-sync', $option->toRequestData());

        if (!$res->successful()) {
            $this->handleErrors($res);
        }

        return $res->json('fileUrl');
    }

    /**
     * Render a PDF in async mode
     * @note Make sure you have set a webhook on RenderPDF.io, otherwise it will always return error
     *
     * @param RenderPdfOptions $option
     *
     * @return true on successfully queued
     *
     * @throws LogicException on missing identifier
     * @throws RenderPdfIoException on render failure
     * @throws ConnectionException on unable to make the API call (local network error)
     */
    public function renderAsync(RenderPdfOptions $option): bool
    {
        if (!$option->identifier) {
            throw RenderPdfIoException::forMissingIdentifierForAsyncFlow();
        }

        $res = $this->getHttpClient()
            ->post('render-async', $option->toRequestData());
        if (!$res->successful()) {
            $this->handleErrors($res);
        }

        return true;
    }

    protected function getHttpClient(): PendingRequest
    {
        return Http::baseUrl('https://renderpdf.io/api/pdfs')
            ->withToken(config('services.renderpdf-io.key'))
            ->asJson();
    }

    protected function handleErrors(Response $response): void
    {
        throw match ($response->status()) {
            429 => RenderPdfIoException::forRateLimited(),
            422 => RenderPdfIoException::forValidationErrors($response->json('errors')),
            default => RenderPdfIoException::forGeneric(),
        };
    }
}
