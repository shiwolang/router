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

    public function invoke()
    {
        return $this->result = $this->callback($this->callback);
    }

    public function invokeHandled()
    {
        if ($this->handled === null) {
            return null;
        }

        return $this->callback($this->handled, ["result" => $this->result]);
    }

    public static function callback($fn, $param = [])
    {
        return [];
    }
}