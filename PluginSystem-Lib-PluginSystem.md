PluginSystem\Lib\PluginSystem
===============

Class PluginSystem




* Class name: PluginSystem
* Namespace: PluginSystem\Lib





Properties
----------


### $instance

    public \PluginSystem\Lib\PluginSystem $instance





* Visibility: **public**
* This property is **static**.


### $actions

    public array $actions





* Visibility: **public**
* This property is **static**.


### $plugins_list

    protected array $plugins_list





* Visibility: **protected**
* This property is **static**.


### $plugins_active

    protected array $plugins_active





* Visibility: **protected**
* This property is **static**.


### $plugins_dir

    protected string $plugins_dir





* Visibility: **protected**
* This property is **static**.


Methods
-------


### __construct

    \PluginSystem\Lib\PluginSystem PluginSystem\Lib\PluginSystem::__construct()

Constructor

The constructor is private to allow for a singleton style object. When the constructor is called it will preload
the active plugins as well as the rest of the plugins in the PluginSystem folder.

* Visibility: **protected**




### instance

    \PluginSystem\Lib\PluginSystem PluginSystem\Lib\PluginSystem::instance()

This function creates a static singleton instance of this class.

If there is an existing loaded version of the PluginSystem that will be returned. Otherwise we will load all the
active and other plugins into a list and return a handle to the newly created PluginSystem.

* Visibility: **public**
* This method is **static**.




### getSystemName

    string PluginSystem\Lib\PluginSystem::getSystemName($name)

Take a human readable name and return the System ID Name for convenient use.

This function will replace spaces with dashes and replace uppercase characters with lowercase in order to generate
the System ID Name that we use to autoload the plugin itself.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $name **mixed** - &lt;p&gt;string The plugin name before formatting (not the human readable name from the header)&lt;/p&gt;



### findPlugins

    mixed PluginSystem\Lib\PluginSystem::findPlugins()

Look through the plugin directory and load the plugins into memory.

This function pulls a list of all folders and files in the plugins directory and then searches for any matches to
the plugin format {system-id-name}\{system-id-name}.php and if it finds a match it will load the headers of the
plugin into a list of all available plugins.

* Visibility: **protected**




### getActivePlugins

    mixed PluginSystem\Lib\PluginSystem::getActivePlugins()

Get Activated Plugins from the database

Load the CakePHP table registry and get an instance of the PluginSystemPlugin table. Grab a list of all active
plugins and load them into the active plugin list. Mark the plugin as active in the all plugins list as well.

* Visibility: **public**




### getPluginHeaders

    mixed PluginSystem\Lib\PluginSystem::getPluginHeaders($plugin)

Load the headers of the plugin into the plugin information in the plugin list.

This function will read the file that is supposed to contain the Plugin Header for the plugin to describe what the
plugin is and who made it. This information is displayed on the activation and de-activation page.

* Visibility: **protected**


#### Arguments
* $plugin **mixed** - &lt;p&gt;string The System ID Name of the plugin&lt;/p&gt;



### loadActive

    mixed PluginSystem\Lib\PluginSystem::loadActive()

Load all of the active plugins and include their files.

Loop through all of the activated plugins and include their main plugin file. This should register all of the
hooks for the plugin so that when called we will have an up to date list of all plugin hooks to run.

* Visibility: **protected**




### pluginList

    array PluginSystem\Lib\PluginSystem::pluginList()

Get a list of plugins that are in the plugins directory.



* Visibility: **public**




### getPlugin

    mixed PluginSystem\Lib\PluginSystem::getPlugin($plugin)

Check for a plugins existence and if it exists then return its information

Check to see if the plugin is on the filesystem and if so return the array containing all of the information
including if it is active and all of its headers.

* Visibility: **public**


#### Arguments
* $plugin **mixed** - &lt;p&gt;string The System ID Name of the plugin&lt;/p&gt;



### activate

    mixed PluginSystem\Lib\PluginSystem::activate($plugin, $id)

Activate the plugin

Make sure that the plugin exists and has been added to the active plugins database. If it has been added then
add it to the list of active plugins in the PluginSystem and call loadActive to make sure we are including the
plugins main php file.

* Visibility: **public**


#### Arguments
* $plugin **mixed** - &lt;p&gt;string The System ID Name for the plugin.&lt;/p&gt;
* $id **mixed** - &lt;p&gt;string The id of the plugin in the database.&lt;/p&gt;



### deactivate

    boolean PluginSystem\Lib\PluginSystem::deactivate($plugin)

Deactivate a plugin by removing it from the list of active plugins.

By removing this from the list of active plugins it will not autoload the php file with hooks and functions and
therefore disable the plugins functionality. This call waits for the controller to successfully delete the plugin
from the database.

* Visibility: **public**


#### Arguments
* $plugin **mixed** - &lt;p&gt;string The System ID Name of the plugin&lt;/p&gt;



### registerHook

    boolean PluginSystem\Lib\PluginSystem::registerHook($hook, $plugin, $function)

Register a function to be called when the hook is called.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $hook **mixed** - &lt;p&gt;string The identifier of the hook&lt;/p&gt;
* $plugin **mixed** - &lt;p&gt;string The System ID Name of the plugin&lt;/p&gt;
* $function **mixed** - &lt;p&gt;string The name of the function to run&lt;/p&gt;



### hook

    array|string|void PluginSystem\Lib\PluginSystem::hook($hook, string $arguments)

Takes the name of a hook and runs all functions associated with it. If there are returnables return them.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $hook **mixed** - &lt;p&gt;string The name of the hook to run&lt;/p&gt;
* $arguments **string** - &lt;p&gt;Any arguments that need to be passed to the hook functions&lt;/p&gt;

