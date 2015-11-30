<?php
/**
 * Created by zhouzhongyuan.
 * User: zhouzhongyuan
 * Date: 2015/11/28
 * Time: 17:49
 */

namespace shiwolang\router;


class Route
{
    protected static $parsers = [
        "parseInt",
    ];


    public $pattern  = "";
    public $rule     = "";
    public $name     = "";
    public $callback = null;
    public $matchs   = [];

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


    public function match($routeString)
    {
        return preg_match($this->pattern, $routeString, $this->matchs);
    }

    public function invoke($context = null)
    {
        $params = $this->matchs;
        array_unshift($params, $context);
        if (is_callable($this->callback)) {
            return call_user_func_array($this->callback, $params);
        }

        return "";
    }

    /**
     * @param string $rule
     */
    public function setRule($rule)
    {
        foreach (static::$parsers as $name => $parser) {
            $rule = $this->$name($rule);
        }
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    function __debugInfo()
    {
        return [
            'pattern' => $this->pattern,
            'rule'    => $this->rule,
            'name'    => $this->name,
        ];
    }
}