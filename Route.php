<?php
/**
 * Created by zhouzhongyuan.
 * User: zhouzhongyuan
 * Date: 2015/11/28
 * Time: 17:49
 */

namespace shiwolang\router;


class Route extends CallBack
{
    protected static $parsers = [
        "parseInt",
    ];


    public $pattern     = null;
    public $rule        = "";
    public $matchs      = [];
    public $routeString = "";

    public function match($routeString)
    {
        $this->routeString = $routeString;

        return preg_match($this->pattern, $routeString, $this->matchs);
    }

    protected function callClassMethod($className, $method, $params)
    {
        $classNameFull = $className . "::" . $method;
        $classNameFull = preg_replace($this->pattern, $classNameFull, $this->routeString);
        $class         = explode("::", $classNameFull);

        return parent::callClassMethod($class[0], $class[1], $params);
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


}