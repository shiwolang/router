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

    public function invokeParent()
    {
        if ($this->parent === null) {
            return null;
        }

        return $this->callback($this->parent, ["result" => $this->result]);
    }

    public static function callback($fn, $param = [])
    {
        if (is_callable($fn)) {
            return call_user_func_array($fn, $param);
        }
    }

    public function __debugInfo()
    {
        return [
            "name"   => $this->name,
            "parent" => $this->parent
        ];
    }
}