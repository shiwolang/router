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
    /**
     * @var \shiwolang\router\CallBack[][]
     */
    protected $contexts = [];
    protected $handlers = [];

    protected $debug = true;

    public static function init($name = "default", $reinit = false)
    {
        if (!isset(self::$_instance[$name]) || $reinit) {
            self::$_instance[$name] = new self();
        } else {
            throw new RouterException("The Router name of (" . $name . ") does exist");
        }

        return self::$_instance[$name];
    }

    public static function instance($name = "default")
    {
        if (isset(self::$_instance[$name])) {
            return self::$_instance[$name];
        } else {
            throw new RouterException("The Router name of (" . $name . ") does not exist");
        }
    }

    public function execute($routeString, $debug = true)
    {
        $this->debug      = $debug;
        $matchedRoute     = $this->routeExecuter($routeString);
        $matchedRouteName = $matchedRoute[0];
        /** @var Route $matchedRoute */
        $matchedRoute = $matchedRoute[1];
        $namesExplode = explode(".", $matchedRouteName);


        $myselfhander = function () use ($matchedRouteName, $matchedRoute, $namesExplode) {
            $this->contextExecuter($namesExplode);
            $result = $matchedRoute->invoke();
            $this->contextExecuter($namesExplode, false);

            return $result;
        };

        $_matchedRouteName      = $namesExplode;
        $_matchedRouteNameCount = count($_matchedRouteName);

        $handers[] = CallBack::init([
            "name"     => '__$$$$$__',
            "callback" => $myselfhander
        ]);;

        for ($i = 1; $i <= $_matchedRouteNameCount; $i++) {
            $name = implode(".", $_matchedRouteName);
            array_pop($_matchedRouteName);
            if (isset($this->handlers[$name])) {
                $handers[] = $this->handlers[$name][1];
            }
        }
        $this->handlerExecuter($handers, count($handers), 1);
    }

    public function routeExecuter($routeString)
    {
        /** @var route[] $matchedRoutes */
        $matchedRoutes = [];
        $context       = null;
        foreach ($this->routes as $name => $route) {
            if ($route->match($routeString)) {
                $matchedRoutes[$name] = $route;
                if (!$this->debug) {
                    break;
                }
            }
        }
        $matchedRoutesCount = count($matchedRoutes);
        if ($matchedRoutesCount > 1) {
            throw new RouterException("Duplicate route pattern match for( " . implode(" );( ", array_keys($matchedRoutes)) . " )");
        }
        if ($matchedRoutesCount == 0) {
            throw new RouterException("Can not match route of " . $routeString . "!", 404);
        }

        return [current(array_keys($matchedRoutes)), current($matchedRoutes)];
    }

    public function contextExecuter($matchedRouteName, $invoke = true)
    {
        $name = "";
        foreach ($matchedRouteName as $key => $namePart) {
            $name .= $key == 0 ? $namePart : "." . $namePart;
            if (isset($this->contexts[$name])) {
                $callbacks = $this->contexts[$name];
                foreach ($callbacks as $callback) {
                    $invoke ? $callback->invoke() : $callback->invokeHandled();
                }
            }
        }
    }

    public function handlerExecuter($handers, $max, $offset = 1)
    {
        /** @var \shiwolang\router\CallBack[] $handers */

        $nextOffset = $offset + 1;
        $nextOffset = $nextOffset >= $max ? null : $nextOffset;

        if ($nextOffset === null) {
            if ($max == 1) {
                return $handers[0]->invoke([]);
            }

            return $handers[$offset]->invoke([$handers[$offset - 1]]);
        } else {
            $handers[$offset]->parent = $handers[$offset - 1];

            return $this->handlerExecuter($handers, $max, $nextOffset);
        }

    }

    public function addRoute(Route $route, $name = null, $reSet = false)
    {
        if ($name === null) {
            $this->routes[] = $route;

            return;
        }
        if ($reSet || !isset($this->routes[$name])) {
            $route->name         = $name;
            $this->routes[$name] = $route;
        } else {
            throw new RouterException("Route of name:(" . $name . ") does exist");
        }
    }

    public function addContext(CallBack $callBack, $name, $append = true)
    {
        if (!isset($this->routes[$name])) {
            throw new RouterException("Route of name:(" . $name . ") does not exist");
        }
        $callBack->name = $name;
        $append ?
            $this->contexts[$name][] = $callBack :
            array_unshift($this->contexts[$name], $callBack);
    }

    public function addHandler(CallBack $callBack, $name, $reSet = false)
    {
        $executeRoute = true;
        if ($reSet) {
            $this->handlers[$name] = [$executeRoute, $callBack];

            return;
        }
        if (isset($this->handlers[$name])) {
            throw new RouterException("Handler of name:(" . $name . ") does exist");
        }

        $callBack->name        = $name;
        $this->handlers[$name] = [$executeRoute, $callBack];
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public static function urlFor($name)
    {

    }

    /////////////////////////////////////////////
    private function __construct()
    {
    }

}