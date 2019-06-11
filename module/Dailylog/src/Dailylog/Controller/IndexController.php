<?php
namespace Dailylog\Controller;
use Dailylog\Model\Label;

use Zend\Session\Container;

use Zend\Validator\Date;
use Dailylog\Model\Mylog;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Dailylog\Controller\AbstractDailyController;
class IndexController extends AbstractDailyController
{
	/**
	 * 
	 * prepare the layout and add the alernative teams for user if he has more than one team;
	 */
	public function mylogAction()
	{ 
		$this->layout()->setVariable('nav',1);
		$this->init();
		$this->layout('layout/dailylog_layout');
		$params=$this->params()->fromQuery();
		if(isset($params['date'])){
			$dateValidator=new Date();
			if($dateValidator->isValid($params['date'])){
				$time=strtotime($params['date']);
				$thedate=date('Y-m-N-d',$time);
				list($year,$month,$day,$date)=explode('-', $thedate);
			}else{
				$thedate=date('Y-m-N-d');
				list($year,$month,$day,$date)=explode('-', $thedate);
			}
		}
		else{
			$thedate=date('Y-m-N-d');
			list($year,$month,$day,$date)=explode('-', $thedate);
		}
		$week=array();
		for ($i=0;$i<=8;$i++)
		{
			$tmpdate=($day>$i)?($date-($day-$i)):$date+$i-$day;
			$time=mktime(0,0,0,$month,$tmpdate,$year);
			$week[$i]=date('Y-m-d',$time);
		}
		$labelTable	=new Label();
		$labellist	=$labelTable->getAllLabels();
		$session	=new Container('user');
		return new ViewModel(array('week'=>$week,'labellist'=>$labellist,'date'=>$year.'-'.$month.'-'.$date,'username'=>$session->username));
		
	}
	public function getweek($date)
	{
		if(isset($date)){
			$dateValidator=new Date();
			if($dateValidator->isValid($date)){
				$time=strtotime($date);
				$year=date('y',$time);
				$month=date('m',$time);
				$day=date('N',$time);
				$date=date('d',$time);
			}
			else {
				$year=date('y');
				$month=date('m');
				$day=date('N');
				$date=date('d');
			}
			
		}else{
			$year=date('y');
			$month=date('m');
			$day=date('N');
			$date=date('d');
		}
		$week=array();
		for ($i=0;$i<=8;$i++)
		{
			$tmpdate=($day>$i)?($date-($day-$i)):$date+$i-$day;
			$time=mktime(0,0,0,$month,$tmpdate,$year);
			$week[$i]=date('Y-m-d',$time);
		}
		return $week;
	}
	public function loadlogAction()
	{
		$params=$this->params()->fromQuery();

		$fromdate=$params['fromdate'];
		$todate	=$params['todate'];
		
		$mylog=new Mylog();
		$myLogData=$mylog->getMylog($fromdate,$todate);
		return new JsonModel($myLogData);
	}
	public function savemylogAction()
	{
		$request	=$this->getRequest();echo $request->isPost();
		if($request->isPost()){
			$data=$this->params()->fromPost();
			$mylog	=new Mylog();
			if($mylog->save($data)){		
				return new JsonModel(array('save_ok'));
			}
		}
		return new JsonModel(array('faild'));
	}
	public function deletelogAction()
	{
		$request=$this->getRequest();
		if($request->isPost()){
			$params=$this->Params()->fromPost();
			
			$mylog=new Mylog();
			$mylog->deleteMylog($params);
			return new JsonModel(array('deleteok'));
		}
		
		return new JsonModel(array('delete fail'));
	}
	public function preweekAction()
	{
		$params=$this->Params()->fromQuery();
		$week=$this->getweek($params['date']);
		$mylog	=new Mylog();
		$mylogdata	=$mylog->getMylog($week[1], $week[7]);
		return new JsonModel(array('week'=>$week,'data'=>$mylogdata));
	}
	public function nextweekAction(){
		$params=$this->Params()->fromQuery();
		$week=$this->getweek($params['date']);
		$mylog	=new Mylog();
		$mylogdata	=$mylog->getMylog($week[1], $week[7]);
		return new JsonModel(array('week'=>$week,'data'=>$mylogdata));
	}
	public function readmylogAction()
	{
		
	}
	public function updatetimeAction()
	{
		$request=$this->getRequest();
		if($request->isPost())
		{
			$date=$request->getPost('date');
			$fromtime=$request->getPost('fromtime');
			$totime=$request->getPost('totime');
			$mylog=new Mylog();
			$result=$mylog->updateTime($date,$fromtime,$totime);
			return new JsonModel(array('result'=>$result));
		}
	}
}
