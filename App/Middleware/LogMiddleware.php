<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        // تسجيل الطلب (logging)
        $logMessage = sprintf(
            "[%s] %s %s\n",
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getPathInfo()
        );

        // حفظ السجل في ملف
        file_put_contents(__DIR__ . '/../../logs/requests.log', $logMessage, FILE_APPEND);

        // استمر في معالجة الطلب
        $response = $next($request);

        return $response;
    }
}
