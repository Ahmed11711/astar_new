<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // تحقق من Content-Type JSON
        if ($response->headers->get('Content-Type') === 'application/json') {
            // ضغط JSON
            $content = gzencode($response->getContent(), 6);
            $response->setContent($content);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', strlen($content));
        }

        return $response;
    }
}
