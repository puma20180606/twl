<?php
namespace Dailylog;
class Module
{
	public function getServiceConfig()
	{
		
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
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