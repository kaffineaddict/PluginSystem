<?php
/**
 * PluginSystem Main Library
 *
 * @author  Blake Sutton <sutton.blake@gmail.com>
 * @version	1.0
 * @since   1.0
 */
namespace PluginSystem\Lib;

use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * Class PluginSystem
 * @package PluginSystem\Lib
 */
class PluginSystem
{
    /**
     * @var PluginSystem A static instance of this class
     */
    public static $instance;

    /**
     * @var array A list of all hooks and the functions linked to them
     */
    public static $actions;

    /**
     * @var array A list of all plugins in the plugins directory
     */
    protected static $plugins_list;

    /**
     * @var array A list of all plugins that are activated
     */
    protected static $plugins_active;

    /**
     * @var string The full path to the plugins directory
     */
    protected static $plugins_dir;
    
    /**
     * Constructor
     *
     * The constructor is private to allow for a singleton style object. When the constructor is called it will preload
     * the active plugins as well as the rest of the plugins in the PluginSystem folder.
     *
     * @return PluginSystem
     */
    protected function __construct() {
    	self::$plugins_dir = Plugin::path('PluginSystem').DS.'src'.DS.'Plugins'.DS;
    	$this->getActivePlugins();
    	$this->findPlugins();
    }
    
    /**
     * This function creates a static singleton instance of this class.
     *
     * If there is an existing loaded version of the PluginSystem that will be returned. Otherwise we will load all the
     * active and other plugins into a list and return a handle to the newly created PluginSystem.
     *
     * @return PluginSystem
     */
    public static function instance()
    {
        // check if there is an instantiated instance
       	if (!self::$instance)
        {
            self::$instance = new PluginSystem();
        }
        return self::$instance;
    }

    /**
     * Take a human readable name and return the System ID Name for convenient use.
     *
     * This function will replace spaces with dashes and replace uppercase characters with lowercase in order to generate
     * the System ID Name that we use to autoload the plugin itself.
     *
     * @param $name string The plugin name before formatting (not the human readable name from the header)
     * @return string The System ID Name
     */
    public static function getSystemName($name) {
    	return str_replace(" ", "-", strtolower(trim($name)));
    }
    /**
     * Look through the plugin directory and load the plugins into memory.
     *
     * This function pulls a list of all folders and files in the plugins directory and then searches for any matches to
     * the plugin format {system-id-name}\{system-id-name}.php and if it finds a match it will load the headers of the
     * plugin into a list of all available plugins.
     */
    protected function findPlugins()
    {   
    	// create a Folder object to the plugins
        $directory = new Folder(self::$plugins_dir);
        // grab the plugins folder directory contents
        $plugins = $directory->read();
        
        // loop through the folders in the directory and check for plugins
        foreach($plugins[0] as $plugin) {
        	$system_name = self::getSystemName($plugin);
    		if(file_exists(self::$plugins_dir.$system_name.DS.$system_name.".php")) {
    			self::$plugins_list[$system_name]['system_name'] = $system_name;
    			$this->getPluginHeaders($system_name);
    		}
    	}
    }
    
    /**
     * Get Activated Plugins from the database
     *
     * Load the CakePHP table registry and get an instance of the PluginSystemPlugin table. Grab a list of all active
     * plugins and load them into the active plugin list. Mark the plugin as active in the all plugins list as well.
     */
    public function getActivePlugins()
    {
    	$plugins = TableRegistry::get('PluginSystemPlugins');
    	$query = $plugins->find();
    	foreach ($query as $row) {
    		$system_name = self::getSystemName($row['system_name']);
    		self::$plugins_active[$system_name]['system_name'] = $system_name;
    		self::$plugins_active[$system_name]['info']['id'] = $row['id'];
    		self::$plugins_active[$system_name]['info']['name'] = $row['name'];
    		self::$plugins_active[$system_name]['info']['uri'] = $row['uri'];
    		self::$plugins_active[$system_name]['info']['author'] = $row['author'];
    		self::$plugins_active[$system_name]['info']['version'] = $row['version'];
    		self::$plugins_active[$system_name]['info']['description'] = $row['description'];
    		// set this to active in the plugin list
    		self::$plugins_list[$system_name]['info']['active'] = true;
    		self::$plugins_list[$system_name]['info']['id'] = $row['id'];
    	}
        // call the include active plugins functino
    	$this->loadActive();
    }
    

	/**
	 * Load the headers of the plugin into the plugin information in the plugin list.
     *
     * This function will read the file that is supposed to contain the Plugin Header for the plugin to describe what the
     * plugin is and who made it. This information is displayed on the activation and de-activation page.
     *
     * @param $plugin string The System ID Name of the plugin
     */
    protected function getPluginHeaders($plugin)
    {
    	$system_name = self::getSystemName($plugin);
    	$file = new File(self::$plugins_dir.$system_name.DS.$system_name.".php");
        $header = $file->read(); // Load the plugin we want
                   
        preg_match ('|Plugin Name:(.*)$|mi', $header, $name);
        preg_match ('|Plugin URI:(.*)$|mi', $header, $uri);
        preg_match ('|Author:(.*)$|mi', $header, $author);
        preg_match ('|Version:(.*)|i', $header, $version);
        preg_match ('|Description:(.*)$|mi', $header, $description);
        
        if (isset($name[1])) { $pluginInfo['name'] = trim($name[1]); }
        if (isset($uri[1])) { $pluginInfo['uri'] = trim($uri[1]); }
        if (isset($author[1])) { $pluginInfo['author'] = trim($author[1]); }
        if (isset($version[1])) { $pluginInfo['version'] = trim($version[1]); }
        if (isset($description[1])) { $pluginInfo['description'] = trim($description[1]); }
            
        foreach ($pluginInfo AS $key => $value)
        {
        	// If the key doesn't exist or the value is not the same, update the array
            if (!isset(self::$plugins_list[$system_name]['info'][$key]) || self::$plugins_list[$system_name]['info'][$key] != $value)
            {
                self::$plugins_list[$system_name]['info'][$key] = trim($value);
            }
            if(!isset(self::$plugins_list[$system_name]['info']['active'])) {
            	self::$plugins_list[$system_name]['info']['active'] = false;
            }
        }
    }

    /**
     * Load all of the active plugins and include their files.
     *
     * Loop through all of the activated plugins and include their main plugin file. This should register all of the
     * hooks for the plugin so that when called we will have an up to date list of all plugin hooks to run.
     */
    protected function loadActive() {
        // return if there are no active plugins
    	if(count(self::$plugins_active) < 1) {
    		return;
    	}
    	foreach(self::$plugins_active as $plugin) {
    		if(file_exists(self::$plugins_dir.$plugin['system_name'].DS.$plugin['system_name'].".php")) {
    			require_once self::$plugins_dir.$plugin['system_name'].DS.$plugin['system_name'].".php";
    		}
    	}
    }

    /**
     * Get a list of plugins that are in the plugins directory.
     *
     * @return array A list of all plugins in the filesystem and their header information.
     */
    public function pluginList() {
    	return self::$plugins_list;
    }
    
    /**
     * Check for a plugins existence and if it exists then return its information
     *
     * Check to see if the plugin is on the filesystem and if so return the array containing all of the information
     * including if it is active and all of its headers.
     *
     * @param $plugin string The System ID Name of the plugin
     * @return mixed An array of plugin information or false if the plugin does not exist.
     */
    public function getPlugin($plugin)
    {
        $system_name = self::getSystemName($plugin); // Make sure the name is lowercase and no spaces

        // check that the plugin exists
        if (isset(self::$plugins_list[$system_name]))
        {
            return self::$plugins_list[$system_name];
        }
        return false;
    }

    /**
     * Activate the plugin
     *
     * Make sure that the plugin exists and has been added to the active plugins database. If it has been added then
     * add it to the list of active plugins in the PluginSystem and call loadActive to make sure we are including the
     * plugins main php file.
     *
     * @param $plugin string The System ID Name for the plugin.
     * @param $id string The id of the plugin in the database.
     */
    public function activate($plugin, $id) {
        $system_name = self::getSystemName($plugin);
        if (isset(self::$plugins_list[$system_name]) && isset(self::$plugins_active[$system_name]))
        {
            self::$plugins_active[$system_name] = self::$plugins_list[$system_name];
            self::$plugins_active[$system_name]['info']['id'] = $id;
            self::$plugins_list[$system_name]['info']['active'] = true;
            $this->loadActive();
        }
    }

    /**
     * Deactivate a plugin by removing it from the list of active plugins.
     *
     * By removing this from the list of active plugins it will not autoload the php file with hooks and functions and
     * therefore disable the plugins functionality. This call waits for the controller to successfully delete the plugin
     * from the database.
     *
     * @param $plugin string The System ID Name of the plugin
     * @return bool True if the deactivation is a success
     */
    public function deactivate($plugin)
    {
        $system_name = self::getSystemName($plugin);
        
        // Okay the plugin exists
        if (isset(self::$plugins_active[$system_name]))
        {
            unset(self::$plugins_active[$system_name]);
        	return true;
        }
        return false;
    }

    /**
     * Register a function to be called when the hook is called.
     *
     * @param $hook string The identifier of the hook
     * @param $plugin string The System ID Name of the plugin
     * @param $function string The name of the function to run
     * @return bool True if the hook already exists.
     */
    public static function registerHook($hook, $plugin, $function)
    {
        // If we have already registered this action return true
        if (isset(self::$actions[$hook][$plugin][$function]))
        {
            return true;
        }
        
        self::$actions[$hook][$plugin][$function] = $function;
        return false; // the hook did not exist
    }

    /**
     * Takes the name of a hook and runs all functions associated with it. If there are returnables return them.
     *
     * @param $hook string The name of the hook to run
     * @param string $arguments Any arguments that need to be passed to the hook functions
     * @return array|string|void Void if the hook does not exist. Otherwise the response from the functions will be returned.
     */
    public static function hook($hook, $arguments = "")
    {
        // check hook exists
        if (!isset(self::$actions[$hook]))
        {
            return false;
        }
        
        foreach(self::$actions[$hook] AS $plugin => $functions)
        {
            foreach($functions AS $function)
	        {
	        	$response = call_user_func_array('\\PluginSystem\\Plugins\\'.$plugin.'\\'.$function, array(&$arguments));
	        	if ($response)
	        	{
	        		$arguments[] = $response;
	        	}
            }
            return $arguments;
        }
    }
}
