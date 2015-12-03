<?php
namespace PluginSystem\Controller;
use PluginSystem\Controller\AppController;

use PluginSystem\Lib;

use Cake\Controller\Controller;
use Cake\Event\Event;

class PluginSystemPluginsController extends Controller
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }
    
    public function index() {
    	$pluginSystem = \PluginSystem\Lib\PluginSystem::instance();
    	
    	$this->set('pluginSystemPlugins', $pluginSystem->pluginList());
    }
	/**
    * Add method
    *
    * @return void Redirects on successful add, renders view otherwise.
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