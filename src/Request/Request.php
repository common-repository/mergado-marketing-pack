<?php declare(strict_types=1);

namespace Mergado\Request;

use Mergado\Traits\SingletonTrait;

class Request
{
    use SingletonTrait;

    public static function getPage()
    {
        return $_GET['page'] ?? false;
    }

    public static function getAction()
    {
        return $_GET['action'] ?? false;
    }

    public static function getToken()
    {
        return $_GET['token'] ?? false;
    }

    public static function getVariable(string $name, $default = false)
    {
        return $_GET[$name] ?? $default;
    }

    public static function getAll(): array
    {
        return $_GET;
    }

}
