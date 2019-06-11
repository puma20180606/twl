<?php
namespace User\Model;
use Zend\Session\Container;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
class UserTable
{
	protected $tableGateway;
	const TABLENAME='user';
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway=$tableGateway;
	}

	/**
	 * 
	 * Get the only one row for specific user
	 */
	public function getOne(array $where)
	{
		$select=new Select();
		$select->from(self::TABLENAME)->where($where)->limit(1);
		return $this->tableGateway->selectWith($select);
	}
	public function insert(array $data)
	{
		return $this->tableGateway->insert($data);
	}
	public function getUserId()
	{
		return $this->tableGateway->getLastInsertValue();
	}
	public function getAdapter()
	{
		return $this->tableGateway->getAdapter();
	}
	public function update($set,$where)
	{
		return $this->tableGateway->update($set,$where);
	}
	public function checkPassword($password)
	{
		$session=new Container('user');
		$result=$this->tableGateway->select(array('userid'=>$session->userId));
		$data=$result->current();
		if($data->pw==md5(STATICSALT.$password.$data->salt)){
			return true;
		}
		return false;
	}
	public function setPassword($userid,$password)
	{
		$prefix=rand(10000, 30000);
		$salt=uniqid($prefix,false);
		$set=array('pw'=>MD5(STATICSALT.$password.$salt),'salt'=>$salt);
		$where=array('userid'=>$userid);
		return $this->tableGateway->update($set,$where);
	}
	public function setUserInfo($userid,$username,$password)
	{
		$prefix=rand(10000, 30000);
		$salt=uniqid($prefix,false);
		$password=md5(STATICSALT.$password.$salt);
		$set=array('name'=>$username,'pw'=>$password,'salt'=>$salt);
		$where=array('userid'=>$userid);
		$this->tableGateway->update($set,$where);
	}
	public function isActive()
	{
		
	}
	public function isExist($email)
	{
		$result=$this->tableGateway->select(function(Select $select)use($email){
			$select->where(array('email'=>$email))->limit(1);
		});
		if($result->count()){
			$user=$result->current();
			return $user;
		}
		return false;
	}
	public function resetPassword($email,$key,$password)
	{
		$user=$this->isExist($email);
		if($user->userid){
		    return	$this->setPassword($user->userid, $password);
		}
	}
}
