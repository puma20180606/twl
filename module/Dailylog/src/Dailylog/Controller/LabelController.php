<?php
namespace Dailylog\Controller;
use Dailylog\Model\Mylog;

use Zend\View\Model\ViewModel;

use Dailylog\Model\Label;

use Zend\Session\Container;

use Zend\View\Model\JsonModel;

class LabelController extends AbstractDailyController
{
	private $session;
	public function indexAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		$labelTable=new Label();
		$labellist=$labelTable->getAllLabels();

		return new ViewModel(array('labellist'=>$labellist));
	}
	public function __construct()
	{
		$this->session=new Container('user');
	}
	public function addAction()
	{
		$request=$this->getRequest();
		if($request->isPost()){
			$lableTable=new Label();
			$lablename=$request->getPost('name');
			$lablecolor=$request->getPost('color');
			$lablecolor=$this->colorformat($lablecolor);
			$lableid=$lableTable->addLabel($lablename,$lablecolor);
			return new JsonModel(array('id'=>$lableid,'color'=>$lablecolor));
		}
		
		return new JsonModel();
	}
	
	public function editAction()
	{
		if($this->getRequest()->isPost())
		{
			$labelid	=$this->params()->fromPost('id');
			$color		=$this->params()->fromPost('color');
			$labelname	=$this->params()->fromPost('labelname');
			
			$labelTable=new Label();
			$result=$labelTable->modify($labelid,$labelname,$this->colorformat($color));
			return new JsonModel(array(($result)?'success':'fail'));
		}
		
		return new JsonModel();
	}
	public function delAction()
	{
		$id=$this->params()->fromPost('id');
		$label=new Label();
		$result=$label->delete($id);
		return new JsonModel(array('result'=>$result?'success':'fail'));
	}
	public function lableAction()
	{
		$this->layout('layout/dailylog_layout');
		return new ViewModel();
	}
	public function isusedAction()
	{
		$label=$this->params()->fromQuery('id');
		$mylog=new Mylog();
		if($mylog->isUsed($label)){
			return new JsonModel(array('used'));
		}
		else{
			return new JsonModel(array('noused'));
		}
	}
	public function mergeAction()
	{
		if($this->getRequest()->isPost())
		{
			$fromlabel	=$this->params()->fromPost('from');
			$tolabel	=$this->params()->fromPost('to');
			$labelTable		=new Label();
			$result=$labelTable->merge($fromlabel,$tolabel);
			return new JsonModel(array(($result)?'success':'fail'));
		}
		$labelid=$this->params()->fromQuery('labelid');
		$labelTable=new Label();
		$result=$labelTable->getStatistics($labelid);
		return new JsonModel(array($result));
	}
	/**
	 * 
	 * when method is get:return the total number of the logs for the labelid
	 * When method is post:update the table for archive logs
	 * @return \Zend\View\Model\JsonModel
	 */
	public function archiveAction()
	{
		if($this->getRequest()->isPost())
		{
			$labelid=$this->params()->fromPost('labelid');
			$labelTable=new Label();
			$result=$labelTable->archive($labelid);
			return new JsonModel(array($result));
		}
		$labelid=$this->params()->fromQuery('labelid');
		$labelTable=new Label();
		$result=$labelTable->getStatistics($labelid);
		return new JsonModel(array($result));
	}
	
	public function colorformat($lablecolor)
	{
		$lablecolor=substr($lablecolor, 4);
		$lablecolor=str_replace(')', '', $lablecolor);
		$lablecolor=explode(',', $lablecolor);
		foreach ($lablecolor as &$color)
		{
			$color=trim($color);
			switch (strlen($color)){
				case 1:$color='00'.$color;break;
				case 2:$color='0'.$color;break;
			}
		}
		$lablecolor=join('', $lablecolor);
		return $lablecolor;	
	}
	public function statisticAction()
	{
		
	}
}