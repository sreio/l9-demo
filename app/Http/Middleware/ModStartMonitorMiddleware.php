<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModStartMonitorMiddleware
{
    /**
     * Log the URL of the execution timeout and record it to the log.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('app.listen_slow_url')) {
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $param = json_encode(\Illuminate\Support\Facades\Request::input());
            $url = $request->url();
            $method = $request->method();
            if ($time >= config('app.listen_slow_url_time')) {
                Log::channel('slow_url')->warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
        }
        return $next($request);
    }
}
