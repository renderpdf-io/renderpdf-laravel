<?php

namespace RenderPdfIo\Tests\Unit;

use RenderPdfIo\RenderPdfIo;
use RenderPdfIo\Services\RenderPdfIoService;
use RenderPdfIo\Services\RenderPdfOptions;
use RenderPdfIo\Tests\TestCase;

class RenderPdfIoTest extends TestCase
{
    public function testFacadeWorksOk()
    {
        $this->app->offsetSet(
            RenderPdfIoService::class,
            $renderService = $this->createMock(RenderPdfIoService::class)
        );

        $renderService->expects($this->once())
            ->method('render')
            ->willReturn('https://fake-url/file.pdf');

        $res = RenderPdfIo::render(new RenderPdfOptions('test'));

        $this->assertSame('https://fake-url/file.pdf', $res);
    }
}
