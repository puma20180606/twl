<?php
namespace Dailylog\Model;
use Zend\Db\ResultSet\ResultSet;

use Zend\Db\Sql\Where;

use Zend\Validator\Date;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Application\Model\AbstractModel;

class Mylog extends AbstractModel
{
	const TABLENAME='mylog';
	private $columns;
	
	public function __construct()
	{
		$this->tablename=self::TABLENAME;
		$this->columns=array(
			'userid',
			'date',
			'fromtime',
			'totime',
			'content' 
		);
	}
	public function save(array $data)
	{
		$sql=new Sql($this->getAdapter());
		$session=new Container('user');
		$dateValidator=new Date();
		
		$checktimeOk=$this->checkTime($data['fromtime']) &&	$this->checkTime($data['totime']);
		if(!$dateValidator->isValid($data['date'])||!$checktimeOk){
			return false;
		}
		if(isset($session->userId)){
			$select=$sql->select(self::TABLENAME);
		/*	$select->where('userid='.$session->userId)
					->where("date = '$data[date]'")
					->where("fromtime = '$data[fromtime]'")
					->where("totime = '$data[totime]'");
		*/	$select->where(array('userid'=>$session->userId,"teamid"=>$session->teamid,"date"=>$data['date'],"fromtime" =>$data['fromtime'],"totime" => $data['totime']));
			$statement=$sql->prepareStatementForSqlObject($select);
			$result=$statement->execute();
			$resultSet=new ResultSet();
			
			$resultSet->initialize($result);
			if($resultSet->count()==0){
				$insert=$sql->insert(self::TABLENAME);
				$insert->columns(array('userid','teamid','date','fromtime','totime','content'));
				$values=array(
					'userid'=>$session->userId,
					'teamid'=>$session->teamid,
					'date'=>$data['date'],
					'fromtime'=>$data['fromtime'],
					'totime'=>$data['totime'],
					'label'	=>$data['label'],
					'content'=>$data['content']
				);
				$insert->values($values);
				$statement	=$sql->prepareStatementForSqlObject($insert);
				$result=$statement->execute();
				return true;
			}
			else{
				$update=$sql->update(self::TABLENAME);
				$update->set(array('content'=>$data['content'],'label'=>$data['label']));
				$update->where('userid='.$session->userId)
						->where("date = '$data[date]'")
						->where("fromtime = '$data[fromtime]'")
						->where("totime = '$data[totime]'");
				$statement=$sql->prepareStatementForSqlObject($update);
				$result=$statement->execute();
			}
			//the following processing about the result from insert
		}
		return false;
	}
	public function checkTime($time){
		if($time>=0 &&$time<=23.5)
			return true;
	}
	public function getMylog($fromdate,$todate)
	{
		$session=new Container('user');
		if(!isset($session->userId)){
			return false;
		}
		$sql=new Sql($this->getAdapter());
		$select	=$sql->select(self::TABLENAME);
		$select->where(array("userid"=>$session->userId))
				->where(array('teamid'=>$session->teamid))
				->where("date>='".$fromdate."'")
				->where("date<='".$todate."'");
		$statement=$sql->prepareStatementForSqlObject($select);
		$result=$statement->execute();
		$resultSet=new ResultSet();
		$resultSet->initialize($result);
		$resultSet=$resultSet->toArray();

		return $resultSet;
	}
	public function deleteMylog($data)
	{
		$session=new Container('user');
		if(!isset($session->userId))return false;
		$userid		=$session->userId;
		$date		=$data['date'];
		$fromtime	=$data['fromtime'];
		$totime		=$data['totime'];
		$sql		=new Sql($this->getAdapter());
		$delete		=$sql->delete(self::TABLENAME);
		$delete->where('userid='.$userid)->where("date = '$date'")->where("fromtime = '$fromtime'")->where("totime = '$totime'");
		$statement	=$sql->prepareStatementForSqlObject($delete);
		$result=$statement->execute();
	}
	public function getAllLogs($date)
	{
		$session=new Container('user');
		$adapter=$this->getAdapter();
		$result=$adapter->query("SELECT profile.portrait,user.name,mylog.* FROM mylog,user LEFT JOIN profile ON user.userid=profile.userid WHERE teamid=? AND date=? AND user.userid=mylog.userid  ORDER  BY mylog.userid,fromtime ASC",array($session->teamid,$date));
		return $result;
	}
	public function updateTime($date,$fromtime,$totime)
	{
		$session	=new Container('user');
		$adapter	=$this->getAdapter();
		$result=$adapter->query('UPDATE mylog SET totime=? WHERE userid=? AND teamid=? AND date=? AND fromtime=?',array($totime,$session->userId,$session->teamid,$date,$fromtime));
		if($result->getAffectedRows()>0)
			return true;
		return false;
	}
	public function isUsed($labelid)
	{
		$adapter	=$this->getAdapter();
		$result=$adapter->query('SELECT label FROM mylog WHERE label=? LIMIT 1',array($labelid));
		if($result->count()>0)
			return true;
		return false;
	}
}