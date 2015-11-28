<?php
/**
 * Created by zhou.
 * User: zhou
 * Date: 2015/11/28
 * Time: 14:52
 */

namespace shiwolang\router;


class Router implements \ArrayAccess
{
    /**
     * @var Router[]
     */
    protected static $_instance = [];

    /**
     * @var Log[]
     */
    protected $log    = [];
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

    private function __construct()
    {

    }

    public function execute($route)
    {

    }

    public function offsetExists($offset)
    {
    }

    public function offsetGet($offset)
    {
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }
}