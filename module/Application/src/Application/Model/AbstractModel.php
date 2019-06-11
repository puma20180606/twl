<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;

class AbstractModel
{
	protected $tablename;
	protected $dbAdapter;
	
	
	public function getAdapter()
	{
		
		if(!$this->dbAdapter){
			$global=include ROOT.'/config/autoload/global.php';
			$local =include ROOT.'/config/autoload/local.php';
			$config	=array_merge($global['db'],$local['db']);
			$this->dbAdapter=new Adapter($config);
		}
		return $this->dbAdapter;
	}
	
}
