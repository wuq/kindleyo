<?php
class IndexController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title = '';
		$this->_layout->nav = 'admin';
		$this->_layout->inc = array();
    }
	
	public function indexAction(){
		/*layout*/
        $this->_layout->meta_title = '点拓后台';
		$this->_layout->inc =array('jq');
		if(Common::checkLogin()){
			Common::isAdmin();
		}
	}
	
	public function loginAction(){
		//error code 1:正常 2:mail null  3:pass null 4:mail not 5:pass error 6:ban
		$user  = new MemberModel();
		if($po = $_POST['u']){
			if(!$po['mail']){echo 2;exit();}
			if(!$po['pass']){echo 3;exit();}
			$find = $user->where("mail = '".trim($po['mail'])."'")->fRow();
			if(!$find){echo 4;exit();}
			if($find['password'] != trim($po['pass'])){echo 5;exit();}
			if(!$find['state']){echo 6;exit();}
			$_SESSION['uid']	  = $find['id'];
			$_SESSION['username'] = $find['username'];
			$_SESSION['admin']    = $find['admin'];
			echo 1;exit();
		}
	}
	
	public function logoutAction(){
		Common::unSession();
		Common::redirect('/admin/index/index');
	}
}