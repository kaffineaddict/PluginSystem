<?php
use Cake\Routing\Router;

Router::plugin('PluginSystem', 
	['path' => '/PluginSystem'],
    function ($routes) {
    	$routes->fallbacks('DashedRoute');
	}
);