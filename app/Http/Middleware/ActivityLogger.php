<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $route = $request->route();
            $data = $request->except([
                '_token',
                'password',
                'password_confirmation',
            ]);

            // Batasi ukuran data request agar log tidak membengkak
            $payload = json_decode(json_encode($data), true);
            $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE);
            if (is_string($encoded) && strlen($encoded) > 4000) {
                $encoded = substr($encoded, 0, 4000);
                $payload = ['truncated' => true];
            }

            ActivityLog::create([
                'user_id' => optional(auth()->user())->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route_name' => $route ? $route->getName() : null,
                'status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
                'request_data' => $data,
            ]);
        } catch (\Throwable $e) {
            // Jangan mengganggu alur aplikasi jika logging gagal
        }

        return $response;
    }
}

