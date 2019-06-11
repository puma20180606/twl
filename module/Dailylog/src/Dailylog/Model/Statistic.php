<?php
namespace Dailylog\Model;
use Zend\Session\Container;
use Application\Model\AbstractModel;
class Statistic extends AbstractModel
{
	public function getDayStatistic($date)
	{
		
	}
	public function getWeekStatistic($year,$week)
	{
		
	}
	public function getMonthStatistic($fromdate,$todate)
	{
		$session=new Container('user');
		$adapter=$this->getAdapter();
		$sql='SELECT RIGHT(date,2) as date,mylog.userid, name, SUM(totime-fromtime) AS sum FROM mylog ,user WHERE teamid=? AND date>=? AND date<? AND mylog.userid=user.userid GROUP BY mylog.userid,mylog.date';
		$result=$adapter->query($sql, array($session->teamid,$fromdate,$todate));
		return $result;
	}
}