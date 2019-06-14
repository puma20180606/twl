<?php
namespace  User\Controller;
use Dailylog\Model\Label;

use Zend\Mime\Part;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Smtp;

use Zend\Mail\Message;
use Dailylog\Controller\AbstractDailyController;
use Zend\Mail\Address;
use User\Model\Profile;
use Zend\Cache\PatternFactory;
use Zend\Log\Writer\Stream;

use Zend\Log\Logger;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Session\Container;
use Zend\I18n\Validator\Alnum;

use Zend\Validator\EmailAddress;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Config\Config;
define('MAX_IMAGE_WIDTH',60);
define('STATICSALT','MouSe');
class IndexController extends AbstractDailyController 
{

	public function mailAction()
	{
		$to='2450329248@qq.com';$header='From:support@icniot.cn\n';
		mail($to,'mail','message',$header);return new JsonView();
	}
	/**
	 * 
	 * Login page showed when user has no priviledge to access the system
	 */
	public function indexAction()
	{
		
		return new ViewModel();
	}
	public function langHandle(){
		$required=array('en_US','zh_CN','ja_JP');
		$lang=$this->params()->fromQuery('lang');
		
		if(array_search($lang, $required)!==false){
			\Locale::setDefault($lang);
			$session=new Container('user');
			$session->lang=$lang;
		}
	}
	/**
	 * 
	 * handle the submit from the user who wanna to login
	 */
	public function loginAction()
	{
		$this->langHandle();
		//get the param from the form
		$request=$this->getRequest();
		if($request->isPost()){
			$email		=$request->getPost('email');
			$emailValidator	=new EmailAddress();
			if(!$emailValidator->isValid($email))
			{
				//$error='Email Format is Wrong!';
				$error='邮箱格式不正确';
				$this->relogin();
				return new ViewModel(array('error'=>$error));
			}
			$password	=$request->getPost('password');
			if(empty($password)){
				$error='Password is needed!';
				$this->relogin();
				return new ViewModel(array('error'=>$error));
			}
			
			$userTable	=$this->getServiceLocator()->get('User\Model\UserTable');
			$dbadapter	=$userTable->getAdapter();
			$authAdapter=new AuthAdapter($dbadapter,'user','email','pw',"MD5(CONCAT('MouSe',?,salt)) AND active=TRUE");
			$authAdapter->setIdentity($email)->setCredential($password);
			$authResult=$authAdapter->authenticate();
			if($authResult->isValid()){
				$session	=new Container('user');
				$result		=$authAdapter->getResultRowObject(array('userid','name'));
				$session->userId	=$result->userid;
				$session->username	=$result->name;
				$session->role		='member';
				$userTable->update(array('lastlogintm'=>null),array('userid'=>$result->userid));
				return $this->redirect()->toRoute('teamlist');
			}else {
				
				$error='账号或密码不正确！';
				$this->relogin();
				return new ViewModel(array('error'=>$error));
			}
		}
		$this->relogin();
		return new ViewModel();
	}
	public function relogin()
	{
		$config=include ROOT.'/config/feature.config.php';
		$order=rand(0, count($config['Backgroundimage'])-1);
		$imagefile=$config['Backgroundimage'][$order];
		$this->layout()->setVariable('imagefile',$imagefile);
		
	}
	/**
	 * 
	 * Handle the logout ,clear the session
	 */
	public function logoutAction()
	{
		$session =new Container('user');
		session_destroy();
		$this->redirect()->toRoute('userlogin');
	}

	public function registerAction()
	{
		$config=include ROOT.'/config/feature.config.php';
		$order=rand(0, count($config['Backgroundimage'])-1);
		$imagefile=$config['Backgroundimage'][$order];
		$layout=$this->layout();
		$layout->setVariable('imagefile',$imagefile);
		if($this->getRequest()->isPost()){
			//validate the input
			$request	=$this->getRequest();
			$email		=$request->getPost('email');
			$emailValidator=new EmailAddress();
			
			if(!$emailValidator->isValid($email)){
				$error	='Email Format is Wrong';
				return new ViewModel(array('error'=>$error));
			}
			$teamName=$request->getPost('teamname');
			if(empty($teamName)){
				$error='Team Name should not be empty!';
				return new ViewModel(array('error'=>$error));
			}
			
			$password	=$request->getPost('password');
			
			if(empty($password)){
				$error	='Password shoud not be empty';
				return new ViewModel(array('error'=>$error));
			}
			$username	=$request->getPost('username');
			//check the input against the DB
			$usertable=$this->getServiceLocator()->get('User\Model\UserTable');
                        $result=$usertable->getOne(array('email'=>$email));
                        //if the user is a new user
			if(!$result->count()){
				//the email input by user is not exist in the database
			//	$teamtable=$this->getServiceLocator()->get('Team\Model\TeamTable');
			//	$result=$teamtable->getOne(array('teamname'=>$teamName));
		//		if($result->count())return false;
				$prefix=rand(10000, 32768);
				$salt=uniqid($prefix,false);
				$data=array(
					'userid'=>null,
					'email'	=>$email,
					'pw'	=>MD5('MouSe'.$password.$salt),
					'salt'	=>$salt,
					'name'	=>$username,
					'active'=>true,
					'createtm'=>null,
					'lastlogintm'=>null,
					'allowed'	=>true,
					'deleted'	=>0
				);                    
                                
				try{
                                    $usertable->insert($data);
				}	
				catch (\Exception $exception){
					//write into the system db log
					echo $exception->getMessage(),$exception->getFile(),$exception->getLine();
					return new ViewModel();
				}
			
                                $userid=$usertable->getTableGateway()->getLastInsertValue();
                               
insertteam:                     $data=array(
					'teamid'=>null,
					'teamname'=>$teamName,
					'createby'	=>$userid,
					'managedby'	=>$userid,
					'createtm'	=>time(),
					'membernum'	=>1,
				);
                         	try {
                                    $teamtable=$this->getServiceLocator()->get('Team\Model\TeamTable');
                                    $teamtable->insert($data);
				}
				catch (\Exception $exception){	
					return new ViewModel(array('Exception'=>$exception->getMessage()));
				}
				$teamid=$teamtable->getTeamId();
				$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
				$verifykey=sha1(uniqid());
				$member=array('teamid'=>$teamid,'memberid'=>$userid,'verifykey'=>$verifykey);
				$memberTable->insert($member);
				$label=new Label();
				$label->initialLabel($teamid);
				//$this->registercomplete(array());
				return	$this->forward()->dispatch('User\Controller\Index',array('action'=>'registercomplete','email'=>$email,'team'=>$teamName,'teamid'=>$teamid,'username'=>$username,'verifykey'=>$verifykey));
			
			/*		
				$session			=new Container('user');
				$session->userId	=$usertable->getUserId();
				$session->username	=$username;
				$session->role		='member';
				$session->teamId	=$teamid;
			*///	return $this->redirect()->toRoute('dailylog');
			}
			else{//the user have been a user ,just to register another team
				$data  =$result->current();
				$userid=$data->userid;
				$pw=md5('MouSe'.$password.$data->salt);
				if($data->pw==$pw){
                                     goto insertteam;
                                }
				else{
				    return new ViewModel(array('error'=>'wrong'));	
					
				}
			}
		}
		return new ViewModel();
	
	}
	public function registercompleteAction()
	{
		$this->layout('layout/layout_fresh');
		global $HOSTNAME;
		$params		=$this->params();
		$team		=$params->fromRoute('team');
		$teamid		=$params->fromRoute('teamid');
		$username	=$params->fromRoute('username');
		$verifykey	=$params->fromRoute('verifykey');
		$email		=$params->fromRoute('email');
		$format=array(
			'en_US'=>"Welcome %s:You have register the team: %s <br>Click here <a href=http://%s/user/index/verify?team=%d&key=%s>http://%s/user/index/verify?team=%d&key=%s</a><br>",
			'zh_Hans_CN'=>"尊敬的用户：%s<br>您已注册团队:%s <br>点击这里，立即激活 <a href=http://%s/user/index/verify?team=%d&key=%s>http://%s/user/index/verify?team=%d&key=%s</a><br>，如果您的邮件阅读程序不支持点击，请将上面的连接拷贝至您的浏览器（如IE）的地址栏后打开",
			'ja_JP'=>"あなたを歓迎します:%s<br>あなたはすでに登録団体:%s <br>ここをクリック <a href=http://%s/user/index/verify?team=%d&key=%s>http://%s/user/index/verify?team=%d&key=%s</a><br>"
		);
		$lang=\Locale::getDefault();
		$body=sprintf($format[$lang],$username,$team,$HOSTNAME,$teamid,$verifykey,$HOSTNAME,$teamid,$verifykey);
		$subject=array('en_US'=>'Team Registering verify','zh_Hans_CN'=>'团队注册认证邮件','ja_JP'=>'团队注册认证');
		$this->sendmail($subject[$lang], $body, $email);
		return new ViewModel(array('email'=>$email));
	}
	public function sendmail($subject,$body,$email)
	{
    
		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\n";

		// Additional headers
		$headers .= 'From: support <support@icniot.cn>' ;
		//mail($email,$subject,$body,$headers);
		//return ;
		$html	=new Part($body);
		$html->type='text/html';
		$body	=new MimeMessage();
		$body->setParts(array($html));
		$message=new Message();
		$message->setBody($body);
		$message->setEncoding("UTF-8");
		$config=$this->getServiceLocator()->get('config');
		$message->setFrom($config['email']['connection_config']['username'],'xiaopang');
		$message->addTo($email);
		$message->setSubject($subject);
		$transport	=new Smtp();
		$options	=new SmtpOptions($config['email']);
		$transport->setOptions($options);
		$transport->send($message);
	}
	public function verifyAction(){
		$request=$this->getRequest();
		$teamid	=$request->getQuery('team');
		$key	=$request->getQuery('key');
		if(!is_numeric($teamid)||strlen($key)!='40'){
			$capture=PatternFactory::factory('capture');
			$capture->start();
			header('HTTP/1.1 403 Forbidden');
		}else{
			$membershipTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
			if($membershipTable->activeMember($teamid,$key))
			{
				$view=new ViewModel(array('result'=>1));
				$this->layout('layout/layout_fresh');
			//	$view->setTerminal(true);
				return $view;
			}
			else{
				$this->redirect()->toRoute('register');
			}
		}
	}
	public function settingAction()
	{
		$this->init();
		$this->layout('layout/dailylog_layout');
		$this->layout()->setVariable('nav',3);
		$session=new Container('user');
		$request=$this->getRequest();
		if($request->isPost()){
			$email	=$this->params()->fromPost('Email');
			$oldpassword=$this->params()->fromPost('OldPassword');
			//if the old password is correct ,the change is allowed
			$emailvalidator	=new Zend\Validator\EmailAddress();
			$alnumvalidator =new Zend\Validator\Alnum();
			if($emailvalidator->isValid($email)&& $alnumvalidator->isValid($oldpassword)){
				//verify the info against the db
			}
			return new ViewModel(array('username'=>$session->username));
		}
		$profile=new Profile();
		$datas=$profile->getProfiles();
		return new ViewModel(array('username'=>$session->username,'profile'=>$datas));
		
	}
	public function changeportraitAction()
	{
		//return new JsonModel();
		$request=$this->getRequest();
		if($request->isPost()){
			$files=$request->getFiles();
			$files=$files->toArray();

			if(is_uploaded_file($files['portrait']['tmp_name'])){
				$date=date('Y-m-d');
				$pulicpathname=ROOT.'/public';
				$imgpathname='/img/'.$date;
				$pathname=$pulicpathname.$imgpathname;
				if(!file_exists($pathname)){
					mkdir($pathname);
				}
				$type=explode('/',$files['portrait']['type']);
				$uniqueName=uniqid('',true).'.'.$type[1];
				$filename=$pathname.'/'.$uniqueName;
				move_uploaded_file($files['portrait']['tmp_name'],$filename);
				$size=$this->resizeImagefile($filename);
				return new JsonModel(array('filename'=>$imgpathname.'/'.$uniqueName));
			}
		}
		return new JsonModel();
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
	public function saveprofileAction()
	{
		$request=$this->getRequest();
		if($request->isPost())
		{
			$data=$request->getPost()->toArray();
			$profile=new Profile();
			$portrait=$profile->saveProfile($data);
			if($data['portrait']&&$portrait){
				$filename=ROOT.'/public'.$portrait;
				if(file_exists($filename)){
					unlink($filename);
				}
			}
		}
		
		return new JsonModel(array('ok'));
	}
	public function changepwAction()
	{
		$request=$this->getRequest();
		if($request->isPost()){
			//get the old password
			$password=$request->getPost('password');
			if(empty($password))return new JsonModel(array('code'=>-1,'reason'=>'Empty password'));
			$usertable=$this->getServiceLocator()->get('User\Model\UserTable');
			if(!$usertable->checkPassword($password)){
				return new JsonModel(array('code'=>-1,'reason'=>'The current password is wrong'));
			}
	
			$params=$this->params();
			$newpassword=$params->fromPost('newpassword');
			$confirmpw	=$params->fromPost('confirmpw');
			if($newpassword<>$confirmpw)
				return new JsonModel(array('code'=>-1,'reason'=>'New password and confirm password are not same'));
			$session=new Container('user');
			$usertable=$this->getServiceLocator()->get('User\Model\UserTable');
			$num=$usertable->setPassword($session->userId,$newpassword);
			return new JsonModel(array('code'=>$num));
		}
	}
	public function changemailAction()
	{
		$request=$this->getRequest();
		if($request->isPost()){
			$params=$this->params()->fromPost();
			$newemail=$params['newemail'];
			$confirmemail=$params['confirmemail'];
			if($newemail<>$confirmemail)
			{
				return new JsonModel(array('code'=>-1,'reason'=>'Provide datas are not Same'));
			}
			$validator=new EmailAddress();
			if(!$validator->isValid($newemail))
			{
				return new JsonModel(array('code'=>-1,'reason'=>'The email format is invalid!'));
			}
			$session=new Container('user');
			$usertable=$this->getServiceLocator()->get('User\Model\UserTable');
			$code=$usertable->update(array('email'=>$newemail),array('userid'=>$session->userId));
			return new JsonModel(array('code'=>$code));
		}
	}
	public function getemailAction()
	{
		$usertable=$this->getServiceLocator()->get('User\Model\UserTable');
		$session=new Container('user');
		$result=$usertable->getOne(array('userid'=>$session->userId));
		$row=$result->current();
		return new JsonModel(array($row->email));
	}

	public function watermarkAction()
	{
		$filename='';
		imagecreatefromjpeg($filename);
	}
	public function confirmAction()
	{
		$this->langHandle();
		$teamid=$this->params()->fromQuery('team');
		$invitekey=$this->params()->fromQuery('key');
		if(is_numeric($teamid)&&is_string($invitekey)&&strlen($invitekey)==40)
		{
			$membershipTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
			$user=$membershipTable->confirm($teamid,$invitekey);
			if(isset($user['userid'])){
				$session=new Container('user');
				$session->userId=$user['userid'];
				$session->teamid=$teamid;
				$session->role		='member';
				return new ViewModel(array('email'=>$user['email'],'team'=>$teamid,'invitekey'=>$invitekey));
			}
			return new ViewModel();
		}
		$capture=PatternFactory::factory('capture',array());
		$capture->start();
		header('HTTP://1.1 400 Bad Request',true,400);
	}
	public function setuserinfoAction()
	{
		if($this->getRequest()->isPost()){
			$session=new Container('user');
			if(isset($session->userId)){
				$params=$this->params();
				$password=$params->fromPost('password');
				$username =$params->fromPost('name');
				$userTable=$this->getServiceLocator()->get('User\Model\UserTable');
				$userTable->setUserInfo($session->userId,$username,$password);
				$session->username=$username;
				//$this->redirect()->toRoute('/dailylog/index/mylog',array('controller'=>'index','action'=>'mylog'));
				$this->redirect()->toUrl('/dailylog/index/mylog');
			}		
		}
		$capture=PatternFactory::factory('capture',array());
		$capture->start();
		header('HTTP://1.1 400 Bad Request',true,400);
	}
	public function forgetpwAction()
	{
		$this->layout('layout/team_layout');
		$flashMessenger=$this->flashMessenger();
		//if($flashMessenger->hasMessages())
		{
			return new ViewModel(array('error'=>$flashMessenger->getMessages()));
		}
		return new ViewModel();
	}
	public function resetpwAction()
	{
		$request=$this->getRequest();
		if($request->isPost())
		{
			$email=$request->getPost('email');
			$password=$request->getPost('password');
			$key=$request->getPost('verifykey');
			$userTable=$this->getServiceLocator()->get('User\Model\UserTable');
			if($userTable->resetPassword($email,$key,$password))
				$this->redirect()->toRoute('userlogin');
		}
		$email=$this->params()->fromQuery('email');
		$verifykey=$this->params()->fromQuery('key');
		return new ViewModel(array('email'=>$email,'verifykey'=>$verifykey));
	}
	public function forgetpwmailAction()
	{
		global $HOSTNAME;
		$request=$this->getRequest();
		if($request->isPost()){
			$email=$request->getPost('email');
			$userTable=$this->getServiceLocator()->get('User\Model\UserTable');
			if($user=$userTable->isExist($email))
			{	$userid=$user->userid;
				$memberTable=$this->getServiceLocator()->get('Team\Model\MembershipTable');
				$verifykey=$memberTable->getVerifykey($userid);
				$lang=\Locale::getDefault();
				$emailbody=array('zh_CN'=>"<div style='left:%s;position:relative;margin-left:-300px;border:2px solid #eee;width:600px;'><div style='background-color:#33b4dc;' ><img src='http://%s/img/p3.png' style='margin-top:15px;margin-bottom:15px;height:60px;margin-left:190px'></div>
		<div style='margin:10px 20px 0 20px'><div>亲爱的用户:%s,您好！<br>
			请点击这里，重置您的密码：<a href=http://%s/user/index/resetpw?email=%s&key=%s>http://%s/user/index/resetpw?email=%s&key=%s</a><br>
			(如果连接无法点击，请将它拷贝到浏览器的地址栏中)<br><br>好的密码，不但应当容易记住，还要尽量符合以下强度：<br>
			<ul><li>包含大小写字母,数字和符号</li><li>不少于10位</li></ul>
			<img style='margin-left:100px' src='http://%s/img/ad.jpg'><br>
			团队日志宗旨：竭尽全力为您的账号安全保驾护航！<br>
			现在就登陆吧！http://%s<br/><br/>
			<div style='text-align'>团队日志&nbsp;敬启</div><br/>
			此为自动发送邮件，请勿直接回复！如您有任何疑问，请点击<a href='http://%s/contact'>联系我们</a>
			</div><br></div><div style='height:30px;background-color:#33b4dc'></div>
		</div>");			

			$body=sprintf($emailbody[$lang],'50%',$HOSTNAME,$user->name,$HOSTNAME,$email,$verifykey,$HOSTNAME,$email,$verifykey,$HOSTNAME,$HOSTNAME,$HOSTNAME);
			$subject=array('en_US'=>'Password Reset','zh_CN'=>'重置密码','ja_JP'=>'リロール暗証番号');				
			$this->sendmail($subject[$lang], $body, $email);
			$this->layout('layout/layout_fresh');
			 return new ViewModel(array('email'=>$email));
			}
			$this->flashMessenger()->addMessage('邮箱地址格式不正确或不存在');
//			return $this->forward()->dispatch('User\Controller\Index',array('action'=>'forgetpw'));
			$this->redirect()->toUrl('/user/index/forgetpw');
		}
	}
}
