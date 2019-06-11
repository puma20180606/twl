<?php
namespace Team\Model;
use Zend\Db\Sql\Select;

use Zend\Db\Sql\Sql;

use Application\Model\AbstractModel;

use Zend\Db\TableGateway\TableGateway;

use Zend\Db\ResultSet\ResultSet;

use Zend\Session\Container;
class MembershipTable extends AbstractModel
{
	public $tablegateway;
	public function __construct(TableGateway $tablegateway)
	{
		$this->tablegateway=$tablegateway;
	}
	public function insert($data)
	{
		$this->tablegateway->insert($data);
	}
	public function getTeamlist()
	{
		$session=new Container('user');
		$adapter=$this->tablegateway->getAdapter();
		$resultSet=$adapter->query('SELECT team.teamid,teamname,createby from team,membership where membership.memberid=? AND membership.teamid=team.teamid',array($session->userId));
		$teamlist=array();
		if($resultSet->count()>0){
			while($item=$resultSet->current())
			{
				array_push($teamlist,$item);	
			}
			return $teamlist;
		}
		return false;
	}
	public function check($teamid)
	{
		$session=new Container('user');
		$result=$this->tablegateway->select(array('memberid'=>$session->userId,'teamid'=>$teamid));
		if($result->count()>0)
			return TRUE;
		return false;
	}
	public function getMemberlist()
	{
		$session=new Container('user');
		$adapter=$this->tablegateway->getAdapter();
		$memberlist=$adapter->query(' SELECT user.*,membership.*,profile.portrait FROM user,membership LEFT JOIN profile on  membership.memberid=userid WHERE membership.teamid=? AND membership.memberid=user.userid AND active=true;',array($session->teamid));
		return $memberlist->toArray();
	}
	public function addMember($email)
	{
		$adapter=$this->getAdapter();
		$sql='INSERT IGNORE user(userid,email,pw,salt,name,active,createtm,lastlogintm,allowed,deleted) VALUES(null,?,?,?,?,?,?,?,?,?)';
		$parameters=array($email,uniqid(),rand(1, 10),'',false,null,0,true,false);
		$result=$adapter->query($sql, $parameters);
		$session=new Container('user');
		if($result->count()==0){
			$sql='SELECT verifykey FROM membership,user WHERE email=? AND user.userid=membership.memberid';
			$parameters=array($email);
			$result=$adapter->query($sql, $parameters);
			return $result->current()->verifykey;
		}
		
		$userid=$adapter->getDriver()->getLastGeneratedValue();
		$verifykey=sha1(uniqid());
		$sql='INSERT membership(teamid,memberid,verifykey)Values(?,?,?)';
		$adapter->query($sql, array($session->teamid,$userid,$verifykey));
		return $verifykey;
	}
	public function confirm($teamid,$verifykey)
	{
		$sql='SELECT memberid FROM membership WHERE teamid=? AND verifykey=?';
		$params=array($teamid,$verifykey);
		$adapter=$this->getAdapter();
		$result=$adapter->query($sql, $params);
		$data=$result->current();
		if($result->count()){
			$sql='UPDATE user SET active=true WHERE userid=?';
			$params=array($data->memberid);
			$adapter->query($sql, $params);
			$sql='SELECT userid,email FROM user WHERE userid=?';
			$params=array($data->memberid);	
			$result=$adapter->query($sql, $params);
			return $result->current();
		}
	}
	/**
	 * 
	 * 1.Activate the user
	 * 2.initiate the label 
	 * @param unknown_type $teamid
	 * @param unknown_type $key
	 */
	public function activeMember($teamid,$key)
	{
		$where	=array('teamid'=>$teamid,'verifykey'=>$key);
		$result=$this->tablegateway->select($where);
		if($result->count())
		{	
			//the guest with the correct verifykey  and teamid,go on to activate the user; 
			$userid=$result->current()->memberid;
			$adapter=$this->getAdapter();
			$sql='UPDATE user SET active=TRUE where userid=?';
			$adapter->query($sql, array($userid));
			return true;
		}
		else {
			return false;
		}
	}
	public function getVerifykey($userid)
	{
		$result=$this->tablegateway->select(function(Select $select)use($userid){
			$select->where(array('memberid'=>$userid))->limit(1);
		});
		$data=$result->current();
		return $data->verifykey;
	}
}
