<?php
namespace Team\Controller;
use Zend\Filter\File\Rename;
use Team\Model\Invite;
use Zend\Cache\PatternFactory;
use Zend\Mime\Part;

use Zend\Validator\NotEmpty;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Mail\Transport\Smtp;

use Zend\Mail\Message;

use Zend\Db\Sql\Ddl\Column\Date;

use Dailylog\Model\Mylog;

use Dailylog\Controller\AbstractDailyController;

use Zend\Session\Container;

use Zend\Crypt\PublicKey\Rsa\PublicKey;

use Zend\View\Model\JsonModel;

use Zend\View\Model\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;
define('MAX_IMAGE_WIDTH', 80);
class IndexController extends AbstractDailyController
{
	const INVITEKEYLENGTH=40;
	/**
	 * 
	 * Basic setting
	 */
	public function indexAction()
	{
		$this->init();
		$this->layout('layout/team_layout');
	
		$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
		if($teamlist=$memberTable->getTeamlist()){
			$view=new ViewModel(array('teamlist'=>$teamlist));
			return $view;
		}
		return new ViewModel();
	}
	/**
	 * 
	 * Invite member to join the team 
	 */
	 public function inviteAction()
	{
		global $HOSTNAME;
		if($this->getRequest()->isPost()){
			//save the emai and invite key into the user table
			$emailAddr=$this->params()->fromPost('email');
			$membershipTable=$membershipTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
			$invitekey=$membershipTable->addMember($emailAddr);
			$session=new Container('user');
			$userTable=$this->getServiceLocator()->get('User\Model\UserTable');
			$sender=$userTable->getOne(array('userid'=>$session->userId));
			$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
			$teamInfo =$teamTable->getTeamInfo();
			$teamname=$teamInfo->teamname;
			$config=$this->getServiceLocator()->get('config');
			$body=$this->params()->fromPost('content');
			$sender=$sender->current();
			$format=array(
				'en_US'=>"<img src='http://%s/img/renwu.jpg'>Invite you to join the team %s ,<br>Click here <a href=http://%s/user/index/confirm?team=%d&key=%s> http://%s/user/index/confirm?team=%d&key=%s</a><br>You can communicate with&nbsp;%s",
				'zh_CN'=>"<img src='http://%s/img/renwu.jpg'>邀请您加入团队%s ,<br>点击这里 <a href=http://%s/user/index/confirm?team=%d&key=%s>http://%s/user/index/confirm?team=%d&key=%s</a><br>您可以通过该邮箱联系&nbsp;%s",
				'ja_JP'=>"<img src='http://%s/img/renwu.jpg'>チームに加わろうとあなたを誘い %s ,<br>ここをクリックしてください <a href=http://%s/user/index/confirm?team=%d&key=%s> http://%s/user/index/confirm?team=%d&key=%s</a><br>あなたが通信することができる&nbsp;%s"
			);
			$subject=array(
				'en_US'=>'Invite to Join Our Team',
				'zh_CN'=>'团队加入邀请函',
				'ja_JP'=>'チームに参加のご案内'
			);
			$lang=\Locale::getDefault();
			$body.=sprintf($format[$lang],$HOSTNAME,$teamname,$HOSTNAME,$session->teamid,$invitekey,$HOSTNAME,$session->teamid,$invitekey,$sender->email);
			
			$html	=new Part($body);
			$html->type='text/html';
			$body	=new MimeMessage();
			$body->setParts(array($html));
			$message=new Message();
			$message->setBody($body);
			$message->setEncoding("UTF-8");
			$message->setFrom($config['email']['connection_config']['username'],'xiaopang');
			$message->addTo($emailAddr);
			$message->setSubject('Invite to Join Our Team');
			$transport	=new Smtp();
			$options	=new SmtpOptions($config['email']);
			$transport->setOptions($options);
			$transport->send($message);
			return new JsonModel();
		}
		return new JsonModel();
	}
	
	public function memberAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		$request=$this->getRequest();
		$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
		if($request->isPost()){
			return new ViewModel();
		}
		$memberlist=$memberTable->getMemberlist();
		$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
		$session=new Container('user');
		$accountowner=$teamTable->getAccountowner($session->teamid);
		return new ViewModel(array('memberlist'=>$memberlist,'accountowner'=>$accountowner));
	}

	public function settingAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
			$this->layout()->setVariable('nav',4);
		$request=$this->getRequest();
		if($request->isPost()){
			$teamname=$request->getPost('teamname');
			$teamlogo=$request->getPost('teamlogo');
			if($teamname||$teamlogo){
				$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
				if($oldlogo=$teamTable->checkTeamlogo()){
					$filename=ROOT.'/public'.$oldlogo;
					unlink($filename);
				}
				if($teamTable->updateName($teamname,$teamlogo)>0){
					return new JsonModel(array('success'));
				}
				else {
					return new JsonModel(array('fail'));
				}
			}
		}
		$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
		$teaminfo=$teamTable->getTeamInfo();
		return new ViewModel(array('teamname'=>$teaminfo->teamname,'teamlogo'=>$teaminfo->logo));
	}
	public function basicsettingAction()
	{
		$this->init();
		$teanName=$this->params()->fromPost('teamname');
		$teamTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
		$teamTable->updae($teamName);
		return new JsonModel(array('ok'));
	}
	
	public function logAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		$this->layout()->setVariable('nav',2);
		$date=$this->params()->fromQuery('date');
		
		$date=($date!=null)?$date:date('Y-m-d');
		$time=strtotime($date);
		$predate=date('Y-m-d',strtotime('-1 day',$time));
		$nextdate=date('Y-m-d',strtotime('+1 day',$time));
		$log=new Mylog();
		$loglist=$log->getAllLogs($date);
		if($loglist->count()>0)
		{
			return new ViewModel(array('loglist'=>$loglist,'date'=>$date,'predate'=>$predate,'nextdate'=>$nextdate));
		}
		else 
		{
			$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
			$memberlist=$memberTable->getMemberlist();
			return new ViewModel(array('loglist'=>$loglist,'memberlist'=>$memberlist,'date'=>$date,'predate'=>$predate,'nextdate'=>$nextdate));	
		}	
	}
	public function changelogoAction()
	{
		if($this->getRequest()->isPost()){
			$files=$this->getRequest()->getFiles();
			$type=explode('/',$files['teamlogofile']['type']);
			if(strtolower($type[0])=='image'){
				$requiredtype=array('jpg','png','bmp','gif','jpeg');
				if(!in_array(strtolower($type[1]), $requiredtype)){
					return new JsonModel(array('filename'=>1));
				}
			}
			else{
				return new JsonModel(array('filename'=>2));
			}
			
			if(is_uploaded_file($files['teamlogofile']['tmp_name'])){
				$date=date('Y-m-d');
				$pulicpathname=ROOT.'/public';
				$imgpathname='/img/'.$date;
				$pathname=$pulicpathname.$imgpathname;
				if(!file_exists($pathname)){
					mkdir($pathname);
				}
				$uniqueName=uniqid('',true);
				$filename=$pathname.'/'.$uniqueName.'.'.$type[1];
				$rename=new Rename($filename);
				$rename->filter($files['teamlogofile']);
				$size=$this->resizeImagefile($filename);
				return new JsonModel(array('filename'=>$imgpathname.'/'.$uniqueName.'.'.$type[1]));
			}
			
		}
		return new JsonModel(array('filename'=>3));
	}
	public function resizeImagefile($filepath){
		
		list($width,$height)=getimagesize($filepath);
		if($width<=MAX_IMAGE_WIDTH)return array('width'=>$width,'height'=>$height);
		$percent=MAX_IMAGE_WIDTH/$width;
	 	$thumbHeight=(int)floor($height*$percent);
		
		$thumb=imagecreatetruecolor(MAX_IMAGE_WIDTH, $thumbHeight);
		$path=pathinfo($filepath);
		$ext=$path['extension'];
		switch ($ext){
			case 'jpeg':
			case 'jpg':
				$source=imagecreatefromjpeg($filepath);
				break;
			case 'png':
				$source=imagecreatefrompng($filepath);
				break;
			case 'gif':
				$source=imagecreatefromgif($filepath);
				break;
			default:
				$source=imagecreatefromgd($filepath);
		}
		imagecopyresized($thumb, $source, 0, 0, 0, 0, MAX_IMAGE_WIDTH, $thumbHeight, $width, $height);
		
		switch ($ext){
			case 'jpeg':
			case 'jpg':
				imagejpeg($thumb,$filepath);
				break;
			case 'png':
				imagepng($thumb,$filepath);
				break;
			case 'gif':
				imagegif($thumb,$filepath);
				break;
			default:
				imagewbmp($thumb,$filepath);
		}
		return array('width'=>MAX_IMAGE_WIDTH,'height'=>$thumbHeight);
	}
	public function handoverAction()
	{
		$request=$this->getRequest();
		if($request->isPost())
		{	
			$teamTable=$this->getServiceLocator()->get('Team\Model\TeamTable');
			$newmanager=$request->getPost('newmanager');
			if(is_numeric($newmanager)){
				if($teamTable->handoverManagement($newmanager)){
					$this->redirect()->toRoute('team');
				}
			}
		}
		$this->redirect()->toRoute('logout');
	}
}
