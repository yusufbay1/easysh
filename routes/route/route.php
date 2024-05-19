<?php

namespace Router;

use App\Libraries\Request;

class Route
{
    private static $routes = array();
    private static $pathNotFound = null;
    private static $methodNotAllowed = null;
    private static $prefix = '';

    public static function get($expression, $function)
    {
        self::addRoute('get', $expression, $function);
    }

    public static function post($expression, $function)
    {
        self::addRoute('post', $expression, $function);
    }

    public static function put($expression, $function)
    {
        self::addRoute('put', $expression, $function);
    }

    public static function delete($expression, $function)
    {
        self::addRoute('delete', $expression, $function);
    }

    public static function patch($expression, $function)
    {
        self::addRoute('patch', $expression, $function);
    }

    public static function prefix($prefix, $callback)
    {
        self::$prefix = $prefix;
        $callback();
        self::$prefix = '';
    }

    public static function getAll()
    {
        return self::$routes;
    }

    public static function pathNotFound($function)
    {
        self::$pathNotFound = $function;
    }

    public static function methodNotAllowed($function)
    {
        self::$methodNotAllowed = $function;
    }

    public static function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false)
    {
        $basepath = rtrim($basepath, '/');
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = '/';
        if (isset($parsed_url['path'])) {
            if ($trailing_slash_matters) {
                $path = $parsed_url['path'];
            } else {
                if ($basepath . '/' != $parsed_url['path']) {
                    $path = rtrim($parsed_url['path'], '/');
                } else {
                    $path = $parsed_url['path'];
                }
            }
        }
        $path = urldecode($path);
        $method = $_SERVER['REQUEST_METHOD'];
        $path_match_found = false;
        $route_match_found = false;
        foreach (self::$routes as $route) {
            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }
            $route['expression'] = '^' . $route['expression'];
            $route['expression'] = $route['expression'] . '$';
            if (preg_match('#' . $route['expression'] . '#' . ($case_matters ? '' : 'i') . 'u', $path, $matches)) {
                $path_match_found = true;
                foreach ((array)$route['method'] as $allowedMethod) {
                    if (strtolower($method) == strtolower($allowedMethod)) {
                        array_shift($matches);
                        if ($basepath != '' && $basepath != '/') {
                            array_shift($matches);
                        }
                        if (is_array($route['function']) && count($route['function']) == 2) {
                            $controller = new $route['function'][0]();
                            $action = $route['function'][1];
                            $function = [$controller, $action];
                        } else {
                            $function = $route['function'];
                        }
                        if (strtolower($method) == 'post' || strtolower($method) == 'put' || strtolower($method) == 'patch') {
                            $request = Request::createFromGlobals();
                            $arguments = array_merge([$request], $matches);
                        } else {
                            $arguments = $matches;
                        }
                        if ($return_value = call_user_func_array($function, $arguments)) {
                            echo $return_value;
                        }
                        $route_match_found = true;
                        break;
                    }
                }
            }
            if ($route_match_found && !$multimatch) {
                break;
            }
        }
        if (!$route_match_found) {
            if ($path_match_found) {
                if (self::$methodNotAllowed) {
                    if ($return_value = call_user_func_array(self::$methodNotAllowed, array($path, $method))) {
                        echo $return_value;
                    }
                }
            } else {
                if (self::$pathNotFound) {
                    if ($return_value = call_user_func_array(self::$pathNotFound, array($path))) {
                        echo $return_value;
                    }
                }
            }
        }
    }

    private static function addRoute($method, $expression, $function)
    {
        $expression = self::$prefix . $expression;
        array_push(self::$routes, array(
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ));
    }
}