<?php
/**
 * Created by zhou.
 * User: zhou
 * Date: 2015/11/28
 * Time: 14:52
 */

namespace shiwolang\router;


class Router
{
    /**
     * @var Router[]
     */
    protected static $_instance = [];

    /**
     * @var Log[]
     */
    protected $log = [];
    /**
     * @var Route[]
     */
    protected $routes = [];

    public static function init($name = "default", $reinit = false)
    {
        if (!isset(self::$_instance[$name]) || $reinit) {
            self::$_instance[$name] = new self();
        } else {
            throw new RouterException("The Router name of (" . $name . ") does exist");
        }

        return self::$_instance[$name];
    }

    public static function m($name = "default")
    {
        if (isset(self::$_instance[$name])) {
            return self::$_instance[$name];
        } else {
            throw new RouterException("The Router name of (" . $name . ") does not exist");
        }
    }

    public function execute($routeString)
    {
        $context = null;
        foreach ($this->routes as $name => $route) {
            if ($route->match($routeString)) {
                $context = $route->invoke($context);
            }
        }
    }

    public function addRoute(Route $route, $name = null, $reSet = false)
    {
        if ($name === null) {
            $this->routes[] = $route;

            return;
        }
        if ($reSet || !isset($this->routes[$name])) {
            $this->routes[$name] = $route;
        } else {
            throw new RouterException("Route of name:(" . $name . ") does exist");
        }
    }

    public function getRoutes()
    {
        return $this->routes;
    }


    /////////////////////////////////////////////
    private function __construct()
    {
    }

}