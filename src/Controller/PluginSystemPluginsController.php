<?php
/**
 * PluginSystem Main Controller to list, activate and deactivate plugins.
 *
 * @author  Blake Sutton <sutton.blake@gmail.com>
 * @version	1.0
 * @since   1.0
 */
namespace PluginSystem\Controller;
use PluginSystem\Controller\AppController;
use PluginSystem\Lib;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Class PluginSystemPluginsController
 * @package PluginSystem\Controller
 */
class PluginSystemPluginsController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Load the flash component so that we can show errors if needed.
     */
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }

    /**
     * Index method
     *
     * Loads all of the plugins in order to print them for the user.
     */
    public function index() {
    	$pluginSystem = \PluginSystem\Lib\PluginSystem::instance();
    	
    	$this->set('pluginSystemPlugins', $pluginSystem->pluginList());
    }

	/**
     * Activate method
     *
     * Attempts to add a plugin to the database and then activate it in the PluginSystem
     *
     * @return void Redirects on successful activation or failure
     */
    public function activate($name)
    {
    	$this->request->allowMethod(['post']);
    	
    	$pluginSystem = \PluginSystem\Lib\PluginSystem::instance();
    	$data = $pluginSystem->getPlugin($name);
        $plugin = $this->PluginSystemPlugins->newEntity();
        
        if($data) {
        	if(!$data['info']['active']) {
            	$plugin = $this->PluginSystemPlugins->patchEntity($plugin, $data['info']);
            	$plugin->system_name = $data['system_name'];
            	if ($this->PluginSystemPlugins->save($plugin)) {
            		$pluginSystem->activate($name, $plugin->id);
                	$this->Flash->success(__('The plugin has been activated.'));
            	} else {
            		$this->Flash->error(__('The plugin could not be activated.'));
            	}
        	} else {
            	$this->Flash->error(__('The plugin is already active.'));
            }
        } else {
        	$this->Flash->error(__('The plugin could not be found.'));
        }
        
        return $this->redirect($this->referer());
    }

    /**
     * Deactivate method
     *
     * Attempts to remove a plugin to the database and then deactivate it in the PluginSystem
     *
     * @return void Redirects on successful deactivate or failure
     */
    public function deactivate($name)
    {
        $this->request->allowMethod(['post', 'delete']);
       
    	$pluginSystem = \PluginSystem\Lib\PluginSystem::instance();
    	$data = $pluginSystem->getPlugin($name);
        $plugin = $this->PluginSystemPlugins->get($data['info']['id']);
        
        if($data) {
        	if ($this->PluginSystemPlugins->delete($plugin)) {
        		$pluginSystem->deactivate($name);
        		$this->Flash->success(__('The plugin has been deactivated.'));
        	} else {
            	$this->Flash->error(__('The plugin could not be deactivated. Please, try again.'));
        	}
        }
        return $this->redirect($this->referer());
    }
}