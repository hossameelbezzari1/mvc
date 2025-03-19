<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        // هنا يمكنك إضافة منطق التحقق من صلاحيات المستخدم
        // مثال: تحقق إذا كان المستخدم قد سجل الدخول
        $isAuthenticated = $this->checkAuthentication($request);

        if (!$isAuthenticated) {
            // إذا لم يكن المستخدم مصرحًا، أعد توجيهه إلى صفحة 403
            return new Response(view('errors/403', ['title' => '403 - Forbidden', 'layout' => 'error']), 403);
        }

        // إذا كان المستخدم مصرحًا، استمر في معالجة الطلب
        return $next($request);
    }

    private function checkAuthentication(Request $request): bool
    {
        // هنا يمكنك إضافة منطق التحقق من تسجيل الدخول
        // مثال بسيط: تحقق من وجود رأس (header) معين
        return $request->headers->get('X-Auth-Token') === 'secret-token';
    }
}