<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAccess;
use App\Services\AccessService;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CheckAccessMiddlewareTest extends TestCase
{
    public function test_allows_request_when_access_service_grants_it(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')->once()->with(null, ['ORDER'])->andReturn(true);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/order-lpk', 'GET');

        $response = $middleware->handle($request, fn ($req) => new \Illuminate\Http\Response('ok'), 'ORDER');

        $this->assertSame('ok', $response->getContent());
    }

    public function test_aborts_with_403_when_access_service_denies_it(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')->once()->with(null, ['ADM'])->andReturn(false);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/security-management', 'GET');

        try {
            $middleware->handle($request, fn ($req) => new \Illuminate\Http\Response('ok'), 'ADM');
            $this->fail('Expected an HttpException to be thrown');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_passes_multiple_codes_through_to_access_service(): void
    {
        $accessService = Mockery::mock(AccessService::class);
        $accessService->shouldReceive('hasAny')
            ->once()
            ->with(null, ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI'])
            ->andReturn(true);

        $middleware = new CheckAccess($accessService);
        $request = Request::create('/checklist-jam-kerja', 'GET');

        $response = $middleware->handle(
            $request,
            fn ($req) => new \Illuminate\Http\Response('ok'),
            'JAM-KERJA-INFURE',
            'JAM-KERJA-SEITAI'
        );

        $this->assertSame('ok', $response->getContent());
    }
}
