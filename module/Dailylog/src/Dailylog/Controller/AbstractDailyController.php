<?php
namespace Dailylog\Controller;
use Zend\Mvc\MvcEvent;

use Zend\EventManager\Event;

use Zend\Session\Container;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractDailyController extends AbstractActionController
{
	/**
	 * 
	 * Get the 2 variables of daily_layout
	 */
	public function init()
	{
		$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
		$teamid=$this->getRequest()->getQuery('teamid');
		//After login using email & password,The teamlist is provide for the user's selection,this teamid is transfered to this action for remember the teamid into the session
		$session=new Container('user');
		if(is_numeric($teamid)&&$memberTable->check($teamid))
		{
			$session->teamid=$teamid;
		}
		else{
			$teamid=$session->teamid;
		}
		$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
		$teamlist=$teamTable->getAllteambyuserid();
		$this->layout()->setVariable('teamlist',$teamlist);
		$this->layout()->setVariable('currentteamid',$teamid);
		$this->layout()->setVariable('userid',$session->userId);
	}
}