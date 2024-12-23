<?php

namespace App\Core;

class Router {
  private const METHOD_GET = "GET";
  private const METHOD_POST = "POST";
  private const METHOD_PUT = "PUT";
  private const METHOD_DEL = "DEL";
  private $routes = [];

  public function get($path, $callback) {
    $this->routes[self::METHOD_GET][$path] = $callback;
  }
  
  public function post($path, $callback) {
    $this->routes[self::METHOD_POST][$path] = $callback;
  }

  public function put($path, $callback) {
    $this->routes[self::METHOD_PUT][$path] = $callback;
  }
  
  public function del($path, $callback) {
    $this->routes[self::METHOD_DEL][$path] = $callback;
  }
  
  public function listen() {
    $path = Request::getPath();
    $method = Request::getMethod();
    $callback = $this->routes[$method][$path] ?? false;

    if($callback === false) {
      header("Location: /_404");
      exit();
    }

    // return call_user_func($callback);
    call_user_func($callback);
  }
}