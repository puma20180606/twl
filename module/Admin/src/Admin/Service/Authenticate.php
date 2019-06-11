<?php
namespace Admin\Service;
use Zend\Log\Writer\Stream;

use Zend\Log\Logger;

use Zend\Http\Headers;
use Zend\Session\Container;
use Zend\Authentication\Storage\Session;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resouce;
class Authenticate 
{
	private $acl;
	private $session;
	public function __construct()
	{
		//definition of ACL
		$this->acl=new Acl();
		$this->acl->addRole('guest')->addRole('member','guest')->addRole('admin','member');
		$this->acl->addResource('Admin');
		$this->acl->addResource('Application');
		$this->acl->addResource('Dailylog');
		$this->acl->addResource('Team');
		$this->acl->addResource('User');
		
	//$this->acl->allow(null,null);
		$this->acl->deny(null,null);
		$this->acl->allow('guest','User','Index.index');
		$this->acl->allow('guest','User','Index.login');
		$this->acl->allow('guest','User','Index.register');
		$this->acl->allow('guest','User','Index.confirm');
		$this->acl->allow('guest','User','Index.verify');
		$this->acl->allow('guest','User','Index.forgetpw');
		$this->acl->allow('guest','User','Index.resetpw');
		$this->acl->allow('guest','User','Index.forgetpwmail');
		$this->acl->allow('guest','Admin','Index.index');
		$this->acl->allow('guest','Admin','Index.login');

		$this->acl->allow('member','User');
		$this->acl->allow('member','Dailylog');
		$this->acl->allow('member','Team');
	}
	public function getSession()
	{
		if(!$this->session){
			$this->session=new Container('user');
		}
		return $this->session;
	}
	/**
	 * 
	 * implement the authentication,show login page if the user is not allowed to accesss the specified action in one controller
	 * @param MvcEvent $e
	 */
	public function doAuthenticate(MvcEvent $e)
	{
	
		$role=(isset($this->getSession()->role))?$this->getSession()->role:'guest';
		$router=$e->getRouter();
		$routematch	=$e->getRouteMatch();
		$controller	=$routematch->getParam('controller');
		if(empty($controller))return ;
		$temp	=explode('\\',$controller);
		$controller	=ucfirst($temp[2]);
		$action		=$routematch->getParam('action');
	 	$class	=get_class($e->getTarget());
	 	$temp	=explode('\\', $class);
	 	$modulename	=ucfirst($temp[0]);
	 	
	 	$logger	=new Logger();
		$writer =new Stream(ROOT."\\data\\error.log");
		$logger->addWriter($writer);
	 	$logger->warn("Role:$role,Modulename:$modulename,Controller:$controller,Action:$action");
	 	
	 	if($this->acl->isAllowed($role,$modulename))
	 	{
	 		return ;
	 	}
		if(!$this->acl->isAllowed($role,$modulename,$controller.'.'.$action)){
			
			switch ($modulename) {
				case 'Admin':
						$routename='adminlogin';
						break;
				default:
						$routename='userlogin';
						break;
			}
			$url=$router->assemble(array(),array('name'=>$routename));
		
			$response = $e->getResponse();
            $response->setStatusCode(302);
            // redirect to login page or other page.
            $response->getHeaders()->addHeaderLine('Location', $url);
            $e->stopPropagation();  
		}
		
	}
}