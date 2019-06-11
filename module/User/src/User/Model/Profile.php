<?php
namespace User\Model;

use Zend\Db\Sql\Sql;

use Zend\Db\ResultSet\ResultSet;

use Application\Model\AbstractModel;

use Zend\Session\Container;

class Profile extends AbstractModel
{
	const TABLENAME='profile';
	private $sql;
	private $session;
	public function __construct(){
		
		$this->session=new Container('user');
		$this->sql=new Sql($this->getAdapter());
	}
	public function getProfiles()
	{	
		$session=new Container('user');
		$select=$this->sql->select(self::TABLENAME);
		$select->where(array('userid'=>$session->userId));
		$statement=$this->sql->prepareStatementForSqlObject($select);
		$result=$statement->execute();
		$result=$result->current();

		return $result;
	}
	public function saveProfile($data)
	{
		//update user table with nickname
		if($data['nickname']){
			$update=$this->sql->update('user');
			$update->set(array('name'=>$data['nickname']));
			$update->where(array('userid'=>$this->session->userId));
			$statement=$this->sql->prepareStatementForSqlObject($update);
			$statement->execute();
		}
		//check the profile 
		if($data['portrait']||$data['phone']){
		$select=$this->sql->select(self::TABLENAME);
		$select->where(array('userid'=>$this->session->userId));
		$statement=$this->sql->prepareStatementForSqlObject($select);
		$result=$statement->execute();
		$datas=$result->current();
		$portrait=$datas['portrait'];
		if($result->count()==0){
			//new data
			$insert=$this->sql->insert(self::TABLENAME);
			$values=array(
				'userid'=>$this->session->userId,
				'portrait'=>($data['portrait']==null)?'':$data['portrait'],
				'phone'=>($data['phone']==null)?'':$data['phone']
			);
			$insert->values($values);
			$statement=$this->sql->prepareStatementForSqlObject($insert);
			$result=$statement->execute();	
			if($result->valid())
			{
				$this->session->username=$data['nickname'];
			}
		}
		else{
			//modify
			$update=$this->sql->update(self::TABLENAME);
			if(empty($data['portrait'])){
				if(empty($data['phone'])){
					return ;
				}
				else {
					$update->set(array('phone'=>$data['phone']));
				}
			}
			else {
				if(empty($data['phone'])){
					$update->set(array('portrait'=>$data['portrait']));
				}
				else {
					$update->set(array('portrait'=>$data['portrait'],'phone'=>$data['phone']));
				}
			}
			$update->where(array('userid'=>$this->session->userId));
			$statement=$this->sql->prepareStatementForSqlObject($update);
			$result=$statement->execute();
			if($result->valid())
			{
				$this->session->username=$data['nickname'];
			}
			
		}
		}
		return $portrait;
	}
	public function getPortrait()
	{
		$session =new Container('user');
		$result=$this->tableGateway->select(function(Select $select)use ($session){
			$select->where(array('userid'=>$session->userId))->limit(1);
		});
		return $result->current()->portrait;
	}
}
