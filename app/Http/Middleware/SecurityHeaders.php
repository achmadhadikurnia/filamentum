<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to apply to every response.
     *
     * @var array<string, string>
     */
    protected array $headers = [
        // HTTPS enforcement (1 year + subdomains)
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',

        // Prevent clickjacking - only allow same-origin framing
        'X-Frame-Options' => 'SAMEORIGIN',

        // Prevent MIME type sniffing
        'X-Content-Type-Options' => 'nosniff',

        // Control referrer information sent to external sites
        'Referrer-Policy' => 'strict-origin-when-cross-origin',

        // Restrict browser features/APIs
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',

        // Isolate browsing context from cross-origin popups
        'Cross-Origin-Opener-Policy' => 'same-origin',

        // Prevent other sites from embedding our resources
        'Cross-Origin-Resource-Policy' => 'same-origin',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        foreach ($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Remove headers that leak server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
