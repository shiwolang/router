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


    public $pattern = null;
    public $rule    = "";
    public $matchs  = [];

    public function match($routeString)
    {
        return preg_match($this->pattern, $routeString, $this->matchs);
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