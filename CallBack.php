<?php
/**
 * Created by zhouzhongyuan.
 * User: zhouzhongyuan
 * Date: 2015/12/2
 * Time: 11:43
 */

namespace shiwolang\router;


class CallBack
{
    public $name     = "";
    public $callback = null;
    public $handled  = null;
    /** @var null|\shiwolang\router\CallBack */
    public $parent = null;

    protected $result    = null;
    protected $exception = null;

    public static function init($config = [])
    {
        $route = new static();

        foreach ($config as $name => $value) {
            $setter = 'set' . ucfirst($name);
            if (method_exists($route, $setter)) {
                $route->$setter($value);
            } else {
                $route->$name = $value;
            }
        }

        return $route;
    }

    public function invoke($params = [])
    {
        if ($this->parent !== null) {
            return $this->result = $this->callback($this->callback, [$this->parent]);
        }

        return $this->result = $this->callback($this->callback, $params);
    }

    public function invokeHandled()
    {
        if ($this->handled === null) {
            return null;
        }

        return $this->callback($this->handled, ["result" => $this->result]);
    }

    public function callback($fn, $param = [])
    {
        if (is_callable($fn)) {
            return $this->callFunction($fn, $param);
        }
        if (is_string($fn) && strpos($fn, "::") !== false) {
            $fn = explode("::", $fn);

            return $this->callClassMethod($fn[0], $fn[1], $param);
        }
        if (is_array($fn)) {
            return $this->callClassMethod($fn[0], $fn[1], $param);
        }

        return null;
    }

    protected function callFunction($fn, $params)
    {
        return null;
    }

    protected function callClassMethod($className, $method, $params)
    {
        return null;
    }

    public function __debugInfo()
    {
        return [
            "name"   => $this->name,
            "parent" => $this->parent
        ];
    }
}