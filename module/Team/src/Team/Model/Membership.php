<?php
namespace Team\Model;

class Membership
{
	public $teamid;
	public $memberid;
	public $verifykey;
	public function exchangeArray($data)
	{
		$this->teamid	=(isset($data['teamid'])?$data['teamid']:null);
		$this->memberid	=(isset($data['memberid'])?$data['memberid']:null);
		$this->verifykey=(isset($data['verifykey']))?$data['verifykey']:null;
	}
	
}