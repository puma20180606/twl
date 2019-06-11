<?php
namespace Admin\Controller;
use Zend\View\Model\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;


class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		
		return new ViewModel();
		
	}
	/**
	 * 
	 * Administor login
	 */
	public function loginAction()
	{
		
	}
	public function updatepriviledgeAction()
	{
		
	}
	public function freshimageconfigAction()
	{
		$this->getServiceLocator()->get('FreshImageConfig')->freshconfig();
		$view=new ViewModel();
	
		return $view;
	}
}