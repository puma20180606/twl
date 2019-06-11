<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	public function onBootstrap(MvcEvent $e)
    {
    	$eventManager        = $e->getApplication()->getEventManager();
    	$eventManager->attach('route',array($this,'authenticate'),2);
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	public function authenticate(MvcEvent $e)
	{
		$servicemanager=$e->getApplication()->getServiceManager();
		$sharedmanager=$e->getApplication()->getEventManager()->getSharedManager();
		$router=$e->getRouter();
		$request=$e->getRequest();
		$match=$router->match($request);
		if(null!=$match){
			$sharedmanager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch',
				function ($e) use ($servicemanager){$servicemanager->get('Authenticate')->doAuthenticate($e);},2);
		}
	}
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
