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

    public function terminate($request, $response): void
    {
        // 测试
        if (app()->runningUnitTests()) {
            return;
        }
        $data = [
            'ip' => $request->ip(),
            'request_data' => $request->all(),
            'response_data' => [], // config('app.env') === 'local' ? $response : null, // 内容太大了 线上就不存储了
            'ms' => round(((microtime(true) - LARAVEL_START)) * 1000, 2),
            'memory' => round(
                (memory_get_usage() - LARAVEL_START_MEMORY) / 1024 / 1024,
                2
            ),
            'headers' => $request->headers->all(),
            'request_uri' => $request->getRequestUri(),
            'code' => $response->getStatusCode(),
        ];
        app('log')->channel('slow_url')->info(json_encode($data));
    }
}
