<?php
/**
 * Plugin Name: Test Plugin
 * Plugin URI: test.plugin.com
 * Version: .01
 * Description: A test plugin verification header
 * Author: Blake Sutton
 */
namespace PluginSystem\plugins\test;
use PluginSystem\Lib;
 
\PluginSystem\Lib\PluginSystem::registerHook('index.view', 'test', 'print_things');
 
function print_things() {
	echo "This is a hello world";
}
