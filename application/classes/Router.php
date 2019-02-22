<?php

class Router
{

	/* mine router */

	private $routes;
	private $route_match;

	public function __construct()
	{
		$this->routes = include(ROOT.'/application/config/routes.php');
	}

	public function checkUri($uri)
	{
		foreach ($this->routes as $route_key => $route_value) {
			if (preg_match("~$route_key~", $uri)) {
				$this->route_match = $route_value;
				return true;
			}
		}
		return false;
	}

	public function run()
	{
		$uri = trim($_SERVER['REQUEST_URI'], '/');
		if ($this->checkUri($uri)) {
				$controller_action_tab = explode('/', $this->route_match);
				$controller_name = ucfirst(array_shift($controller_action_tab)).'Controller';
				$action_name = array_shift($controller_action_tab).'Action';
				$controller_path = ROOT.'/application/controllers/'.$controller_name.'.php';

				if (file_exists($controller_path)) {
					include_once($controller_path);
					$new_controller_object = new $controller_name;
					$new_controller_object->$action_name();
				} else {
					include_once(ROOT.'/application/views/error404.php');
				}
		} else {			
			include_once(ROOT.'/application/views/error404.php');
		}
	}
}