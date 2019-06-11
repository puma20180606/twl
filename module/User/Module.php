<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use User\Model\User;
use User\Model\UserTable;
class Module
{
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
    public function getServiceConfig()
    {
    	return array(
    		'factories'=>array(
    			'User\Model\UserTable'=>function ($sm){
    				$tablegateway	=$sm->get('UserTableGateway');
    				$usertable=new UserTable($tablegateway);
    				return $usertable;
    			},
    			'UserTableGateway'=>function ($sm){
    			
    				$adapter	=$sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype	=new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new User());
    				return new TableGateway('user', $adapter,null,$resultSetPrototype);
    			},
    		),
    	);
    }
}
