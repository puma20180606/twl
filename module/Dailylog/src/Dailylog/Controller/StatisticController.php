<?php
namespace Dailylog\Controller;

use Zend\View\Model\JsonModel;

use Zend\Crypt\PublicKey\Rsa\PublicKey;

use Dailylog\Model\Statistic;

use Zend\View\Model\ViewModel;

class StatisticController extends AbstractDailyController
{
	public function labelAction()
	{
		
	}
	public function userAction()
	{
		
	}
	public function dayAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		$date=$this->params()->fromQuery('date');
		$statistic=new Statistic();
		$result=$statistic->getDayStatistic($date);
		return new ViewModel(array($result));
	}
	public function weekAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		
	}
	public function monthAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		//
		if($this->getRequest()->isPost()){
			$date=$this->params()->fromPost('date');
		}
		else{
			$date=$this->params()->fromQuery('date');
		}
		
		if(empty($date)){
			$date=date('Y-m-d');
		}
		$datearr=$this->getMonthDur($date);
	
		$statistic=new Statistic();
		$result=$statistic->getMonthStatistic($datearr['fromdate'], $datearr['todate']);
		$temp	=explode('-', $date);
		$numofdays	=$this->getMonthdays($temp[0], $temp[1]);
		if($this->getRequest()->isPost())
		{
			$result=$result->toArray();
			//print_r($result);
			return new JsonModel(array('numofdays'=>$numofdays,'fromdate'=>$datearr['fromdate'],'todate'=>$datearr['todate'],'data'=>$result));
		}
		return new ViewModel(array('numofdays'=>$numofdays,'date'=>$date,'fromdate'=>$datearr['fromdate'],'todate'=>$datearr['todate'],'data'=>$result));
	}
	public function yearAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
	}
	public function getMonthdays($year,$month)
	{
		if($year%4==0&&$year%100!=0||$year%400==0)
		{
			$leap=true;
		}
		switch ($month){
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
				return 31;
			case 2:
				if($leap){
					return 29;
				}
				else{
					return 28;
				}
			default:return 30;
		}
	}
	public function getMonthDur($date)
	{
		$date=explode('-',$date);
		$month=$date[1]-0;
		$fromdate=$date[0].'-'.$date[1].'-'.'1';
		if(12==$date[1]){
			$date[0]=$date[0]+1;
			$date[1]=1;
		}
		else {
			$date[1]=$date[1]+1;
		}
		$todate=$date[0].'-'.$date[1].'-'.'1';
		return array('fromdate'=>$fromdate,'todate'=>$todate);
	}
}