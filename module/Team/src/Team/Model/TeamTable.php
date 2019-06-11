<?php
namespace Team\Model;
use Zend\Session\Container;

use Zend\Db\Sql\Select;

use Zend\Db\TableGateway\TableGateway;

class TeamTable
{
	public $tablegateway;
	public $table;
	public function __construct(TableGateway $tablegateway)
	{
		$this->table='team';
		$this->tablegateway=$tablegateway;		
	}
	public function insert($data){
		return $this->tablegateway->insert($data);
	}
	public function getOne(array $where)
	{
		$select=new Select();
		$select->from($this->table)->where($where)->limit(1);
		return $this->tablegateway->selectWith($select);
	}
	public function getTeamId()
	{
		return $this->tablegateway->getLastInsertValue();
	}
	public function updateName($name,$teamlogo)
	{
		$session=new Container('user');
		$set=array('teamname'=>$name,'logo'=>$teamlogo);
		$where=array('teamid'=>$session->teamid);
		return $this->tablegateway->update($set,$where);
	}
	public function getAccountowner($teamid)
	{
		$result=$this->tablegateway->select(array('teamid'=>$teamid));
		$data=$result->current();
		return $data->managedby;
	}
	public function getAllteambyuserid()
	{
		$session=new Container('user');
		$adapter=$this->tablegateway->getAdapter();
		$result=$adapter->query('SELECT team.teamid,teamname,managedby,logo FROM team,membership WHERE membership.memberid=? AND membership.teamid=team.teamid',array($session->userId));
		return $result->toArray();
	}
	public function getTeamInfo()
	{
		$session=new Container('user');
		$result=$this->tablegateway->select(array('teamid'=>$session->teamid));
		$data=$result->current();
		return $data;
	}
	public function handoverManagement($newmanager)
	{
		$session=new Container('user');
//		$result=$this->tablegateway->select(array('teamid'=>$session->teamid,'managedby'=>$session->userId));
		$set=array('managedby'=>$newmanager);
		$where=array('teamid'=>$session->teamid,'managedby'=>$session->userId);
		$result=$this->tablegateway->update($set,$where);
		if($result)
			return true;
		return false;
	}
	public function checkTeamlogo()
	{
		$session=new Container('user');
		$result=$this->tablegateway->select(function(Select $select)use ($session){
			$select->where(array('teamid'=>$session->teamid))->limit(1);
		});
		$data=$result->current();
		return $data->logo;
	}
}