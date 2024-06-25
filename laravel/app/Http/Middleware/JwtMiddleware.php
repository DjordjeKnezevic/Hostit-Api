<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

class JwtMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = $guards ?: ['api-jwt'];
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            Log::warning('JWT Middleware: Unauthenticated request.', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        } catch (\Exception $e) {
            Log::error('JWT Middleware: Error parsing token.', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Token parsing error'], 401);
        }

        return $next($request);
    }
}
