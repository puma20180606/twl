<?php
namespace Team;
use Team\Model\Membership;
use Team\Model\MembershipTable;
use Team\Model\TeamTable;
use Team\Model\Team;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
class Module
{
    public function getConfig()
    {
    	return include __DIR__ . '/config/module.config.php';
    }
	public function getServiceConfig()
	{
		return array(
    		'factories'=>array(
    			'Team\Model\TeamTable'=>function ($sm){
    				$tablegateway	=$sm->get('TeamTableGateway');
    				$teamtable=new TeamTable($tablegateway);
    				return $teamtable;
    			},
    			'TeamTableGateway'=>function ($sm){
    				$adapter	=$sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype	=new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new Team());
    				return new TableGateway('team', $adapter,null,$resultSetPrototype);
    			},
    			'Team\Model\MembershipTable'=>function ($sm){
    				$tablegateway	=$sm->get('MembershipTableGateway');
    				$membershiptable=new MembershipTable($tablegateway);
    				return $membershiptable;
    			},
    			'MembershipTableGateway'=>function ($sm){
    				$adapter	=$sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype	=new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new Membership());
    				return new TableGateway('membership', $adapter,null,$resultSetPrototype);
    			},
    			
    		),
    	);
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
