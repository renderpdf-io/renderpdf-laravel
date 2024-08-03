<?php

namespace RenderPdfIo\Exceptions;

use RuntimeException;

class RenderPdfIoException extends RuntimeException
{
    public static function forGeneric(): self
    {
        return new self('Failed to render your PDF file');
    }

    public static function forValidationErrors(array $errorsBag): self
    {
        $firstErrors = array_shift($errorsBag);

        return new self($firstErrors[0]);
    }

    public static function forRateLimited(): self
    {
        return new self('You have exceeded the API usage for the current minute.');
    }

    public static function forMissingIdentifierForAsyncFlow(): self
    {
        return new self('You must set an unique identifier when using the async mode.');
    }
}
