<?php

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\VarDumper\VarDumper;

// view
function view($viewName, $data = [])
{
    extract($data);

    ob_start();
    require __DIR__ . "/Views/{$viewName}.php";
    $content = ob_get_clean();

    if (isset($layout)) {
        ob_start();
        require __DIR__ . "/Views/layouts/{$layout}.php";
        $finalContent = ob_get_clean();
        return $finalContent;
    }

    if (strpos($viewName, 'errors/') === 0 && !isset($layout)) {
        ob_start();
        require __DIR__ . "/Views/layouts/error.php";
        $finalContent = ob_get_clean();
        return $finalContent;
    }

    return $content;
}

function extend($layout)
{
    $GLOBALS['layout'] = $layout;
}

function section($name)
{
    ob_start();
    $GLOBALS['section_' . $name] = true;
}

function endSection()
{
    $content = ob_get_clean();
    $GLOBALS['section_content'] = $content;
}

function renderSection($name)
{
    if (isset($GLOBALS['section_' . $name])) {
        echo $GLOBALS['section_content'];
    }
}

function config($key, $default = null)
{
    static $config = [];

    if (empty($config)) {
        $configFiles = glob(__DIR__ . '/../config/*.php');
        foreach ($configFiles as $file) {
            $config[basename($file, '.php')] = require $file;
        }
    }

    $keys = explode('.', $key);
    $value = $config;
    foreach ($keys as $keyPart) {
        if (!isset($value[$keyPart])) {
            return $default;
        }
        $value = $value[$keyPart];
    }

    return $value;
}

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

// دالة route() لتوليد الروابط بناءً على اسم المسار
function route(string $name, array $parameters = []): string
{
    static $generator = null;
    static $routes = null;

    // تحميل المسارات مرة واحدة فقط
    if ($routes === null) {
        $routes = require __DIR__ . '/../routes/web.php';
    }

    // إعداد UrlGenerator
    if ($generator === null) {
        $context = new RequestContext();
        $generator = new UrlGenerator($routes, $context);
    }

    try {
        return $generator->generate($name, $parameters);
    } catch (\Exception $e) {
        throw new \Exception("Route '$name' not found or invalid parameters.");
    }
}

// دالة dd() المحاكاة باستخدام Symfony VarDumper
if (!function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            $file = $backtrace['file'];
            $line = $backtrace['line'];
            ob_start();
            VarDumper::dump($arg);
            $output = ob_get_clean();
            echo "<pre>// {$file}:{$line}\n" . $output . "</pre>";
        }
        exit(1);
    }
}

// فئة Log المحاكاة
class Log
{
    protected static $logFile = __DIR__ . '/../storage/logs/app.log';

    public static function info($message, $context = [])
    {
        self::writeLog('INFO', $message, $context);
    }

    public static function error($message, $context = [])
    {
        self::writeLog('ERROR', $message, $context);
    }

    protected static function writeLog($level, $message, $context)
    {
        // إنشاء المجلد إذا لم يكن موجودًا
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $logMessage = sprintf(
            "[%s] [%s] %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}


