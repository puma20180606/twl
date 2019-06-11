<?php
namespace Dailylog\Model;
use Zend\Session\Container;

use Zend\Db\Sql\Sql;

use Application\Model\AbstractModel;

class Label extends AbstractModel
{
	const NORECORD=-1;
	public function addLabel($lablename,$lablecolor)
	{
		$session=new Container('user');
		$adapter=$this->getAdapter();
		$result=$adapter->query('INSERT label(labelid,teamid,labelname,color) VALUES(null,?,?,?)',array($session->teamid,$lablename,$lablecolor));
		$lableid=$result->getGeneratedValue();
		return $lableid;
	}
	public function modify($labelid,$labelname,$color)
	{
		$adapter=$this->getAdapter();echo $color;echo '---',$labelid;
		if(empty($labelname)){
			$result=$adapter->query('UPDATE label SET color=? WHERE labelid=?', array($color,$labelid));
			return $result;
		}
		$result=$adapter->query('UPDATE label SET labelname=?,color=? WHERE labelid=?', array($labelname,$color,$labelid));
		return $result;
	}
	public function getAllLabels()
	{
		$session=new Container('user');
		$adapter=$this->getAdapter();
		$result=$adapter->query('SELECT * FROM label WHERE teamid=?', array($session->teamid));
		return $result->toArray();
	}
	public function delete($labelid)
	{
		//delete all log belong to the labelid ,then delete the labelid itself
		$adapter=$this->getAdapter();
		$result=$adapter->query('DELETE FROM mylog WHERE label=?',array($labelid));
		$result=$adapter->query('DELETE FROM label WHERE labelid=?', array($labelid));
		return $result->count();
	}
	public function merge($fromlabelid,$tolabelid)
	{
		$adapter=$this->getAdapter();
		//modify the labelid of mylog table to the specified $labelid
		$result=$adapter->query('UPDATE mylog SET label=? WHERE label=?', array($tolabelid,$fromlabelid));
		return $result;
	}

	public function archive($labelid)
	{
		$adapter=$this->getAdapter();
	/*	$result=$adapter->query('SELECT label FROM mylog WHERE label=? LIMIT 1', array($labelid));
		if($result->count()==0)
		{
			return self::NORECORD;
		}
	*/
		$result=$adapter->query('UPDATE mylog SET archive=true WHERE label=?', array($labelid));
		return $result;
	}
	public function getStatistics($labelid)
	{
		$adapter=$this->getAdapter();
		$result=$adapter->query('SELECT COUNT(1) AS COUNT FROM mylog WHERE label=?', array($labelid));
		$row=$result->current();
		return $row['COUNT'];
	}
	public function initialLabel($teamid)
	{
		$adapter=$this->getAdapter();
		$result=$adapter->query('SELECT teamid FROM label WHERE teamid=? LIMIT 1', array($teamid));
		if($result->count())return ;
		$locale=\Locale::getDefault();
		$adapter->query('INSERT label(labelid,teamid,labelname,color)VALUES(null,?,"Label 1","000051102")', array($teamid));
	}
}