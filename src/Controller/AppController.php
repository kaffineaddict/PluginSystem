<?php
 /**
  * PluginSystem AppController - Parent Controller
  *
  * Eventually this can be used to load components to help or aid with other configuration when the PluginSystem is expanded.
  * At this time there is no need to globally register or load the helper library.
  *
  * @author  Blake Sutton <sutton.blake@gmail.com>
  * @version 1.0
  * @since   1.0
  */
namespace PluginSystem\Controller;

use App\Controller\AppController as BaseController;

    /**
 * Class AppController
 * @package PluginSystem\Controller
 */

class AppController extends BaseController
{

    /**
     * Initialization hook method.
     *
     * This function is not needed as we do not at this time have any initialization needed for the plugin controllers.
     * In the future it is possible that I will add a globalization of the PluginSystem object but that could lead
     * to many security issues
     * 
     * @return void
     */
    public function initialize()
    {
    	// do nothing at this time.
    }
}
