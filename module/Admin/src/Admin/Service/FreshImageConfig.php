<?php
namespace Admin\Service;
use Zend\Config\Config;

use Zend\Config\Writer\PhpArray;

class FreshImageConfig
{
	public   $bgimagedir;
	private  $configfile;
	public function __construct()
	{
		$this->bgimagedir=ROOT.'/public/img/bgimg';
		$this->configfile=ROOT.'/config/feature.config.php';
	}
	public function freshconfig()
	{
		$handle=opendir($this->bgimagedir);
		$filelist=array();
		while(false!==($filename=readdir($handle)))
		{
			if($filename!='.' && $filename!='..')
				array_push($filelist,$filename);
			
		}
		closedir($handle);	
		$config=new Config(array(),true);
		$config->Backgroundimage=$filelist;
		$writer=new PhpArray();
		$writer->toFile($this->configfile, $config);
	}
	
	
}