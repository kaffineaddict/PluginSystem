<?php
namespace PluginSystem\Controller\Component;

// get path to plugin
use Cake\Core\Plugin;
// helpers for file/folder
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

use Cake\Event\Event;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * PluginSystem component
 */
class PluginSystemComponent extends Component
{
	// the path to this plugin
	protected $_plugin_system_root;
	// the path of the plugins folder to store other plugins
	protected $_plugin_folder;	
	
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
    	'plugin_dir' => 'plugins/'
    ];
    
    /**
     * 
     * Options:
     * 		plugin_dir: path to plugin folder relaative to PluginSystem Root (default: plugins/)
     */
    public function initialize(array $config) {
    	$this->_plugin_system_root = Plugin::path('PluginSystem');
    	
    	if(isset($config['plugin_dir'])) {
    		if(file_exists($this->_plugin_system_root.$config['plugin_dir'])) {
    			$this->config('plugin_dir', $config['plugin_dir']);
    		}
    	} else {
    		$this->config('plugin_dir', $this->config('plugin_dir'));
    	}
    	$this->_plugin_folder = new Folder($this->_plugin_system_root.$this->config('plugin_dir'), true, 0755);
    }
    
    /**
     * The startup function called after beforeFilter
     */
    public function startup(Event $event) { }
    
    public function list_plugins() {
    	$plugin_configs = [ ];
    	$files = $this->_plugin_folder->read();
    	
    	foreach($files[0] as $file) {
    		if(file_exists($this->_plugin_folder->path.$file.DS.$file.".php")) {
    			$plugin_configs[] = $this->_plugin_folder->path.$file.DS.$file.".php";
    		}
    	}
    	foreach($files[1] as $file) {
    		$ext = pathinfo($file, PATHINFO_EXTENSION);
    		if($ext == "php") {
    			$plugin_configs[] = $this->_plugin_folder->path.$file;
    		}
    	}
    	
    	die(var_dump($plugin_configs));
    }

    /**
    * Set Plugin Dir
    * Set the location of where all of the plugins are located
    * 
    * @param mixed $directory
    */
    public function set_plugin_dir($directory)
    {
        if (!empty($directory))
        {
            $this->plugins_dir = trim($directory);
        }    
    }
    
    /**
    * Find Plugins
    * 
    * Find plugins in the plugins directory. 
    * 
    */
    public function find_plugins()
    {        
        $plugins = directory_map($this->plugins_dir, 1); // Find plugins
        
        if ($plugins != false)
        {        
            foreach ($plugins AS $key => $name)
            {                 
                $name = strtolower(trim($name));
                      
                // If the plugin hasn't already been added and isn't a file
                if (!isset(self::$plugins_pool[$name]) AND !stripos($name, "."))
                {              
                    // Make sure a valid plugin file by the same name as the folder exists
                    if (file_exists($this->plugins_dir.$name."/".$name.".php"))
                    {
                        // Register the plugin
                        self::$plugins_pool[$name]['plugin'] = $name; 
                    }
                    else
                    {
                        self::$errors[$name][] = "Plugin file ".$name.".php does not exist.";
                    }
                }
            }
        }
    }
    
    /**
    * Include Plugins
    * Include all active plugins that are in the database
    * 
    */
    public function include_plugins()
    {
        if(self::$plugins_active AND !empty(self::$plugins_active))
        {
            // Validate and include our found plugins
            foreach (self::$plugins_active AS $name => $value)
            {
                // The plugin information being added to the database
                $data = array(
                    "plugin_system_name" => $name,
                    "plugin_name"        => trim(self::$plugins_pool[$name]['plugin_info']['plugin_name']),
                    "plugin_uri"         => trim(self::$plugins_pool[$name]['plugin_info']['plugin_uri']),
                    "plugin_version"     => trim(self::$plugins_pool[$name]['plugin_info']['plugin_version']),
                    "plugin_description" => trim(self::$plugins_pool[$name]['plugin_info']['plugin_description']),
                    "plugin_author"      => trim(self::$plugins_pool[$name]['plugin_info']['plugin_author']),
                    "plugin_author_uri"  => trim(self::$plugins_pool[$name]['plugin_info']['plugin_author_uri'])
                );
                $this->_ci->db->where('plugin_system_name', $name)->update('plugins', $data);
            
                // If the file was included
                include_once $this->plugins_dir.$name."/".$name.".php";				
            
                // Run the install action for this plugin
                self::do_action('install_' . $name); 
            }   
        }
    }
}
