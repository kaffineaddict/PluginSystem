<?php
use Cake\Routing\Router;

Router::plugin('PluginSystem', function ($routes) {
    $routes->fallbacks('DashedRoute');
});
