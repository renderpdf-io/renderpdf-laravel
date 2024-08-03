<?php

namespace RenderPdfIo\Tests\Unit;

use GuzzleHttp\Psr7\Response as BaseResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response as LaravelResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RenderPdfIo\Exceptions\RenderPdfIoException;
use RenderPdfIo\Services\RenderPdfIoService;
use RenderPdfIo\Services\RenderPdfOptions;
use RenderPdfIo\Tests\TestCase;

class RenderPdfIoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set([
            'services.renderpdf-io.key' => 'fake-api-key',
        ]);
    }

    public static function renderReturnsErrorDataProvider(): array
    {
        return [
            'validation error' => [
                new LaravelResponse(new BaseResponse(
                    422,
                    body: json_encode([
                        'errors' => [
                            'html_content' => [
                                'Invalid HTML Content'
                            ],
                        ],
                    ])
                )),
                'Invalid HTML Content'
            ],

            'rate limit error' => [
                new LaravelResponse(new BaseResponse(
                    429,
                    body: null
                )),
                'You have exceeded the API usage for the current minute.'
            ],

            'generic error' => [
                new LaravelResponse(new BaseResponse(
                    400,
                    body: json_encode([
                        'outcome' => 'RENDER_PDF_FAILED',
                    ])
                )),
                'Failed to render your PDF file'
            ],
        ];
    }

    /**
     * @dataProvider renderReturnsErrorDataProvider
     */
    public function testRenderReturnsError(LaravelResponse $response, string $expectErrorMsg): void
    {
        $this->expectException(RenderPdfIoException::class);
        $this->expectExceptionMessage($expectErrorMsg);

        Http::shouldReceive('baseUrl')
            ->andReturn($fakePendingRequest = $this->createMock(PendingRequest::class));

        $fakePendingRequest->expects($this->once())
            ->method('withToken')
            ->with('fake-api-key')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('asJson')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('post')
            ->with(
                'render-sync',
                []
            )
            ->willReturn($response);

        $service = new RenderPdfIoService();
        $service->render(new RenderPdfOptions(''));
    }

    public function testRenderReturnsFileUrl(): void
    {
        Http::shouldReceive('baseUrl')
            ->andReturn($fakePendingRequest = $this->createMock(PendingRequest::class));

        $fakePendingRequest->expects($this->once())
            ->method('withToken')
            ->with('fake-api-key')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('asJson')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('post')
            ->with(
                'render-sync',
                [
                    'htmlContent' => 'hihi',
                ]
            )
            ->willReturn(new LaravelResponse(new BaseResponse(
                200,
                body: json_encode([
                    'outcome' => 'SUCCESS',
                    'fileUrl' => 'https://fake-url/file.pdf',
                ])
            )));

        $service = new RenderPdfIoService();
        $fileUrl = $service->render(new RenderPdfOptions('hihi'));

        $this->assertSame(
            'https://fake-url/file.pdf',
            $fileUrl
        );
    }

    public function testRenderAsyncThrowsErrorBecauseMissingIdentifier(): void
    {
        $this->expectException(RenderPdfIoException::class);
        $this->expectExceptionMessage('You must set an unique identifier when using the async mode.');

        $service = new RenderPdfIoService();
        $status = $service->renderAsync(new RenderPdfOptions(
            'hihi',
        ));

        $this->assertTrue($status);
    }

    public function testRenderAsyncThrowsErrorOnFailed(): void
    {
        $this->expectException(RenderPdfIoException::class);

        Http::shouldReceive('baseUrl')
            ->andReturn($fakePendingRequest = $this->createMock(PendingRequest::class));

        $fakePendingRequest->expects($this->once())
            ->method('withToken')
            ->with('fake-api-key')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('asJson')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('post')
            ->with(
                'render-async',
                [
                    'htmlContent' => 'hihi',
                    'identifier' => 'hehe',
                ]
            )
            ->willReturn(new LaravelResponse(new BaseResponse(
                400,
                body: json_encode([
                    'outcome' => 'QUEUE_FAILED',
                ])
            )));

        $service = new RenderPdfIoService();
        $service->renderAsync(new RenderPdfOptions(
            'hihi',
            identifier: 'hehe'
        ));
    }

    public function testRenderAsyncReturnsOk(): void
    {
        Http::shouldReceive('baseUrl')
            ->andReturn($fakePendingRequest = $this->createMock(PendingRequest::class));

        $fakePendingRequest->expects($this->once())
            ->method('withToken')
            ->with('fake-api-key')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('asJson')
            ->willReturnSelf();

        $fakePendingRequest->expects($this->once())
            ->method('post')
            ->with(
                'render-async',
                [
                    'htmlContent' => 'hihi',
                    'identifier' => 'hehe',
                ]
            )
            ->willReturn(new LaravelResponse(new BaseResponse(
                200,
                body: json_encode([
                    'outcome' => 'SUCCESS',
                    'fileUrl' => 'https://fake-url/file.pdf',
                ])
            )));

        $service = new RenderPdfIoService();
        $status = $service->renderAsync(new RenderPdfOptions(
            'hihi',
            identifier: 'hehe'
        ));

        $this->assertTrue($status);
    }
}
