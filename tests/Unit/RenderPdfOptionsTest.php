<?php

namespace RenderPdfIo\Tests\Unit;

use RenderPdfIo\Services\RenderPdfOptions;
use RenderPdfIo\Tests\TestCase;

class RenderPdfOptionsTest extends TestCase
{
    public function testToRequestDataIncludesThePresentedValues(): void
    {
        $data = new RenderPdfOptions(
            htmlContent: 'test',
            headerHtmlContent: 'hello',
            footerHtmlContent: 'world',
            marginBottom: '9in',
            landscape: true
        );

        $this->assertEquals([
            'htmlContent' => 'test',
            'headerHtmlContent' => 'hello',
            'footerHtmlContent' => 'world',
            'landscape' => true,
            'marginBottom' => '9in',
        ], $data->toRequestData());
    }
}
