PluginSystem\Controller\PluginSystemPluginsController
===============

Class PluginSystemPluginsController




* Class name: PluginSystemPluginsController
* Namespace: PluginSystem\Controller
* Parent class: Cake\Controller\Controller







Methods
-------


### initialize

    mixed PluginSystem\Controller\PluginSystemPluginsController::initialize()

Initialization hook method.

Load the flash component so that we can show errors if needed.

* Visibility: **public**




### index

    mixed PluginSystem\Controller\PluginSystemPluginsController::index()

Index method

Loads all of the plugins in order to print them for the user.

* Visibility: **public**




### activate

    void PluginSystem\Controller\PluginSystemPluginsController::activate($name)

Activate method

Attempts to add a plugin to the database and then activate it in the PluginSystem

* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;String The System ID Name of the plugin to activate&lt;/p&gt;



### deactivate

    void PluginSystem\Controller\PluginSystemPluginsController::deactivate($name)

Deactivate method

Attempts to remove a plugin to the database and then deactivate it in the PluginSystem

* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;String The System ID Name of the plugin to activate&lt;/p&gt;

