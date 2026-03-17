<?php

namespace IGedeon\WompiLaravel\Http\Middleware;

use Closure;
use IGedeon\WompiLaravel\Services\WebhookSignatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function __construct(private readonly WebhookSignatureService $signatureService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->signatureService->verify($request->all())) {
            abort(403, 'Invalid Wompi webhook signature.');
        }

        return $next($request);
    }
}
