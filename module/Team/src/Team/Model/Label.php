<?php
namespace Team\Model;
use Application\Model\AbstractModel;

class Label extends AbstractModel
{
	public function addLabel($teamid,$labelname)
	{
		$adapter=$this->getAdapter();
		$adapter->query('INSERT ', $parametersOrQueryMode);
	}
	public function delLabel($teamid,$labelid)
	{
		
	}
	public function modLable($teamid,$labelid)
	{
		
	}
	
}