<?php
namespace User\Model;
class User
{
	public $userid;
	public $email;
	public $pw;
	public $salt;
	public $name;
	public $active;
	public $createtm;
	public $lastlogintm;
	public $allowed;
	public $deleted;
	public function exchangeArray($data)
	{
		$this->userid	=(isset($data['userid']))?$data['userid']:null;
		$this->email	=(isset($data['email']))?$data['email']:'';
		$this->pw		=(isset($data['pw']))?$data['pw']:'';
		$this->salt		=(isset($data['salt']))?$data['salt']:'';
		$this->name		=(isset($data['name']))?$data['name']:'';
		$this->active	=(isset($data['active']))?$data['active']:false;
		$this->createtm	=(isset($data['createtm']))?$data['createtm']:'';
		$this->lastlogintm	=(isset($data['lastlogintm']))?$data['lastlogintm']:null;
		$this->allowed	=(isset($data['allowed']))?$data['allowed']:false;
		$this->deleted	=(isset($data['deleted']))?$data['deleted']:false;
	}
}