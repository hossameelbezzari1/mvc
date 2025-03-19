<?php

namespace App\Routing;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\LogMiddleware;

class Route
{
    protected static $routes;
    protected static $middleware = [
        'auth' => AuthMiddleware::class,
        'log' => LogMiddleware::class,
    ];
    protected static $groupMiddleware = []; // إعلان الخاصية الثابتة
    protected $route;

    public static function __callStatic($method, $args)
    {
        if (!static::$routes) {
            static::$routes = new RouteCollection();
            require_once __DIR__ . '/../../routes/web.php';
        }

        [$uri, $action] = $args;
        $methods = strtoupper($method) === 'ANY' ? ['GET', 'POST', 'PUT', 'DELETE'] : [strtoupper($method)];

        $route = new NamedRoute($uri, [
            'controller' => $action instanceof \Closure ? [static::class, 'handleClosure'] : $action,
            'closure' => $action instanceof \Closure ? $action : null,
        ], [], [], '', [], $methods);

        // تطبيق الميدل وير من المجموعة إذا كان موجودًا
        if (!empty(static::$groupMiddleware)) {
            $route->middleware(static::$groupMiddleware);
        }

        $routeInstance = new static();
        $routeInstance->route = $route;

        static::$routes->add($uri, $route);

        return $routeInstance;
    }

    public static function get($uri, $action)
    {
        return static::__callStatic('get', [$uri, $action]);
    }

    public static function post($uri, $action)
    {
        return static::__callStatic('post', [$uri, $action]);
    }

    public static function group(array $options, \Closure $callback)
    {
        if (!static::$routes) {
            static::$routes = new RouteCollection();
        }

        // حفظ الميدل وير السابق
        $previousMiddleware = static::$groupMiddleware;
        if (isset($options['middleware'])) {
            static::$groupMiddleware = array_merge(static::$groupMiddleware, (array) $options['middleware']);
        }

        // تنفيذ الدالة الخاصة بالمجموعة
        call_user_func($callback);

        // استعادة الميدل وير السابق
        static::$groupMiddleware = $previousMiddleware;
    }

    public function name($name)
    {
        if (!$this->route) {
            throw new \Exception('No route to name.');
        }

        $this->route->name($name);

        $routes = static::$routes->all();
        foreach ($routes as $key => $r) {
            if ($r === $this->route) {
                static::$routes->remove($key);
                static::$routes->add($name, $r);
                break;
            }
        }

        return $this;
    }

    public function middleware($middleware)
    {
        if (!$this->route) {
            throw new \Exception('No route to apply middleware to.');
        }

        $this->route->middleware((array) $middleware);
        return $this;
    }

    public static function handleClosure(Request $request, $closure)
    {
        $response = $closure($request);
        if (is_string($response)) {
            return new Response($response);
        }
        return $response;
    }

    public static function getRoutes()
    {
        if (!static::$routes) {
            static::$routes = new RouteCollection();
            require_once __DIR__ . '/../../routes/web.php';
        }
        return static::$routes;
    }
}
