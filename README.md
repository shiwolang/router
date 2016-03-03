# RapidRouter 轻量级路由组件

* 正则路由
* 非侵入式
* 以业务为中心进行嵌套和上下文包装
* 单纯的路由分发不和HTTP等耦合

##使用方式

###通过composer安装
```
$ composer require shiwolang/route
```
###初始化路由容器
#####单个路由情景
```php
Router::init();
```
#####多个路由情景
```php
Router::init("route");
Router::init("route1");
```
###获取路由容器
```php
$router  = Router::instance();
$router0 = Router::instance("route");
$router1 = Router::instance("route1");
```
###添加路由
```php
$router->addRoute(Route::init([
    "pattern"  => "#^php$#",
    "callback" => function () {
        return "nihao123";
     },
     "abstract" => true
]), "app.php");
$router->addRoute(Route::init([
    "pattern"  => "#^php/id/([a-z])+#",
    "callback" => HelloController::class . "::" . "nihao",
]), "app.php.a");
```
###添加路由处理器
```php
$router->addHandler(CallBack::init([
    "callback" => function (CallBack $result) {
        $result->invoke();
        echo "55\n";
    }
]), "app.php");
$router->addHandler(CallBack::init([
    "callback" => function (CallBack $result = null) {
        $result->invoke();
        echo "66\n";
    }
]), "app");
$router->addContext(CallBack::init([
    "callback" => function () {
        echo "11\n";
    },
    "handled"  => function () {
        echo "33\n";
    }
]), "app.php.a");
```
###路由执行
```php
Router::instance()->execute("php/id/aff");
Router::instance("route")->execute("php/id/aff");
```
###组件使用思路
    以业务作为中心，进行路由分发执行，并以路由为中心，进行嵌套和上下文的包装。
    自由使用方式，轻松解决，分模块，分组等纠结的问题，一切以业务作为中心进行非侵入式的封装。