<?php
/**
 * Plugin Name: Hello World
 * Plugin URI: helloworld.plugin.com
 * Version: 1.01
 * Description: A test plugin for PluginSystem
 * Author: Blake Sutton
 *
 *
 * For a plugin to be recognized and listed in the system you must come up with a system id name. The system will allow
 * spaces and capitalization but will change them behind the scenes. This system name will be used to autoload the
 * the functions through namespace as well as to include the main file. A valid plugin must have the following file
 * {system-id-name}\{system-id-name}.php to be auto loaded. For example HelloWorld system id name will autoload only if
 * helloworld\helloworld.php exists. If you wanted to use the system name of Hello World then the file
 * hello-world\hello-world.php must exist. The name in the header is the human readable name and there are no restrictions
 * it can be different then the underlying system id name as it is in this example plugin.
 *
 * A proper plugin will need to have its own namespace the format for proper autoloading is:
 * PluginSystem\plugins\{system-id-name}
 *
 * @package PluginSystem\plugins\helloworld
 */
namespace PluginSystem\plugins\helloworld;
use PluginSystem\Lib;

// This will load the hook system that allows us to register a function
\PluginSystem\Lib\PluginSystem::registerHook('index.view', 'helloworld', 'print_things');

/**
 * A simple function to print hello world as an example
 */
function print_things() {
    	echo "This is a hello world plugin";
}