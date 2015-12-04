<?php
 /**
  * This file will setup the routing for PluginSystem to allow for a simplistic and standard path for plugin system controllers and actions.
  *
  * The routes included will allow you to contact the PluginSystem controller using the /Plugins prefix. It also comes pre-loaded with
  * a /Plugins/Settings controller path that will link to the PluginSystemPlugins controller that will be used to activate and deactivate
  * the plugins themselves.
  *
  * @author 	Blake Sutton <sutton.blake@gmail.com>
  * @version 	1.0
  */
use Cake\Routing\Router;

/**
 *
 */
Router::plugin('PluginSystem', 
	['path' => '/Plugins'],
    function ($routes) {
    	$routes->fallbacks('DashedRoute');
    	
    	$routes->connect('/Settings', ['controller' => 'PluginSystemPlugins']);
    	$routes->connect('/Settings/:action/*', ['controller' => 'PluginSystemPlugins']);
	}
);