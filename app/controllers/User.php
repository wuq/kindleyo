<?php
class UserController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = '看雨阅读-账号管理';
		$this->_layout->keyword     = Common::keyword();
		$this->_layout->description = Common::description();
		$this->_layout->inc = ['j10'];
		$this->_layout->mod = '';
    }
	
	public function regAction(){
		if($this->http()->isPost()){
			//error 1:邮箱错误 2:邮箱存在 3:昵称错误 4:密码错误 5:验证码错误 6:ok
			$p = [];
			foreach($_POST as $k => $v) $p[$k] = trim($v);
			$mp = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+.com+/';
			$np = '/^[0-9A-Za-z_]{4,16}$/';
			$pp = '/^[0-9A-Za-z_]{6,16}$/';
			preg_match($mp,$p['mail']) || exit('1');
			preg_match($np,$p['nick']) || exit('3');
			preg_match($pp,$p['pwd']) || exit('4');
			($p['verify'] == $_SESSION['verifycode']) || exit('5');
			$p['cdate'] = date('Y-m-d H:i:s');
			$p['pwd']   = md5($p['pwd']);
			$_u = new UserModel();
			($_u->ffMail($p['mail'])) && exit('2');			
			if($uid = $_u->insert($p)){
				Yaf_Session::getInstance()->set('u_mail', $p['mail']);
				Yaf_Session::getInstance()->set('u_id', $uid);
				Yaf_Session::getInstance()->set('u_nick', $p['nick']);
				exit('6');
			}
			throw new Exception('注册失败,请重试');
		}
	}
	
	public function captchaAction(){
		$v = new verify();
		echo $v();
		exit(0);
	}
	
	public function loginAction(){
		(Yaf_Session::getInstance()->get('u_id')) && $this->redirect('/');		
		if($this->http()->isPost()){
			//error 1:邮箱错误 2:邮箱不存在 3:密码错误  4:ok
			$p = [];
			foreach($_POST as $k => $v) $p[$k] = trim($v);	
			$mp = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+.com+/';
			$pp = '/^[0-9A-Za-z_]{6,16}$/';
			preg_match($mp,$p['mail']) || exit('1');
			preg_match($pp,$p['pwd']) || exit('3');
			$pwd = md5($p['pwd']);
			$_u  = new UserModel();
			($_u->ffMail($p['mail'])) || exit('2');
			$user = $_u->where("mail = '".$p['mail']."' AND pwd = '".$pwd."'")->fRow();
			if($user){
				Yaf_Session::getInstance()->set('u_mail', $user['mail']);
				Yaf_Session::getInstance()->set('u_id', $user['id']);
				Yaf_Session::getInstance()->set('u_nick', $user['nick']);
				exit('4');
			}
			throw new Exception('注册失败,请重试');			
		}
	}
	
	public function logoutAction(){
		Yaf_Session::getInstance()->del('u_mail');
		Yaf_Session::getInstance()->del('u_nick');
		Yaf_Session::getInstance()->del('u_id');
		$this->redirect('/');
	}
}