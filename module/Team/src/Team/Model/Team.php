<?php
namespace Team\Model;
class Team
{
	public $teamid;
	public $teamname;
	public $createby;
	public $managedby; 
	public $createtm;
	public $membernum;

	public $logo;
	public function exchangeArray($data)
	{
		$this->teamid=(isset($data['teamid'])?$data['teamid']:null);
		$this->teamname=(isset($data['teamname'])?$data['teamname']:'');
		$this->createby=(isset($data['createby'])?$data['createby']:null);
		$this->managedby=(isset($data['managedby'])?$data['managedby']:null);
		$this->createtm=(isset($data['createtm'])?$data['createtm']:null);
		$this->membernum=(isset($data['membernum'])?$data['membernum']:1);
		$this->logo		=(isset($data['logo'])?$data['logo']:null);
	}
}


 
         