<?php
namespace PluginSystem\Lib;

// get path to plugin
use Cake\Core\Plugin;
// get the plugins table
use Cake\ORM\TableRegistry;
// helpers for file/folder
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * PluginSystem
 */
class PluginSystem
{
    // instance of this class for singleton
    public static $instance;
    
    // a list of hook functions
    public static $actions;
    
    // plugins
    protected static $plugins_list;
    protected static $plugins_active;
    
    // plugin storage directory
    protected static $_plugins_dir;
    
    /**
     * Constructor
     * 
     * @param mixed $params
     * @return null
     */
    protected function __construct() {
    	self::$_plugins_dir = Plugin::path('PluginSystem').DS.'src'.DS.'Plugins'.DS;
    	$this->getActivePlugins();
    	$this->findPlugins();
    }
    
    /**
     * This function creates a static singleton instance of this class.
     */
    public static function instance()
    {
    	
       	if (!self::$instance)
        {
            self::$instance = new PluginSystem();
        }
        return self::$instance;
    }
    
    public static function getSystemName($name) {
    	return str_replace(" ", "-", strtolower(trim($name)));
    }
    /**
     * Look through the plugin directory and load the plugins into memory
     */
    protected function findPlugins()
    {   
    	// create a Folder object to the plugins
        $directory = new Folder(self::$_plugins_dir);
        // grab the plugins folder directory contents
        $plugins = $directory->read();
        
        // loop through the folders in the directory and check for plugins
        foreach($plugins[0] as $plugin) {
        	$system_name = self::getSystemName($plugin);
    		if(file_exists(self::$_plugins_dir.$system_name.DS.$system_name.".php")) {
    			self::$plugins_list[$system_name]['system_name'] = $system_name;
    			$this->getPluginHeaders($system_name);
    		}
    	}
    }
    
    /**
    * Get Activated Plugins from the database
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
    	$this->loadActive();
    }
    

	/**
	 * Load the headers of the plugin into the plugin information
	 * in the plugin list
	 */
    protected function getPluginHeaders($plugin)
    {
    	$system_name = self::getSystemName($plugin);
    	$file = new File(self::$_plugins_dir.$system_name.DS.$system_name.".php");
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
    
    protected function loadActive() {
    	if(count(self::$plugins_active) < 1) {
    		return;
    	}
    	foreach(self::$plugins_active as $plugin) {
    		if(file_exists(self::$_plugins_dir.$plugin['system_name'].DS.$plugin['system_name'].".php")) {
    			require_once self::$_plugins_dir.$plugin['system_name'].DS.$plugin['system_name'].".php";
    		}
    	}
    }
    
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
    
    public function pluginList() {
    	return self::$plugins_list;
    }
    
    /**
    * Get Plugin
    *
    * Activates a plugin only if it exists in the
    * plugins_pool. After activating, reload page
    * to get the newly activated plugin
    * 
    * @param mixed $name
    */
    public function getPlugin($plugin)
    {
        $system_name = self::getSystemName($plugin); // Make sure the name is lowercase and no spaces

        // Okay the plugin exists
        if (isset(self::$plugins_list[$system_name]))
        {
            return self::$plugins_list[$system_name];
        }
        return false;
    }
    
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
    
    public static function registerHook($hook, $plugin, $function)
    {
        // If we have already registered this action return true
        if (isset(self::$actions[$hook][$plugin][$function]))
        {
            return true;
        }
        
        self::$actions[$hook][$plugin][$function] = $function;
    }
    
    public static function hook($hook, $arguments = "")
    {
        // check hook exists
        if (!isset(self::$actions[$hook]))
        {
            return;
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
