<?php
class SetController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = Yaf_Session::getInstance()->get('u_nick');
		$this->_layout->keyword     = '';
		$this->_layout->description = '';
		$this->_layout->mod = 'book';
		$this->_layout->inc = array('j10');
		$this->_layout->url = $this->http()->getRequesturi();
		Common::checkLogin();		
    }
	
	
	//
	public function signAction(){
		if(!$uid = Yaf_Session::getInstance()->get('u_id')) throw new Exception('403 forbidden');
		$_u = new UserModel();
		($_u->where('id = '.$uid)->update($_POST)) && exit('ok');
		exit('err');
	}
	
	public function avatarAction(){
		if(!$id = Yaf_Session::getInstance()->get('u_id')) throw new Exception('403 forbidden');
		$_u = new UserModel();
		$this->_layout->me = $_u->fRow($id);
	}
	
	public function faceAction(){
		$uid = Yaf_Session::getInstance()->get('u_id');
		$savePath    = 'avatar/';  
		$savePicName = $uid.'_'.time(); 
		
		$filename162 = $savePath.$savePicName."_162.jpg"; 
		$filename48  = $savePath.$savePicName."_48.jpg"; 
		
		$pic1 = base64_decode($_POST['pic1']);   
		$pic2 = base64_decode($_POST['pic2']);  

		file_put_contents($filename162, $pic1);
		file_put_contents($filename48, $pic2);

		$rs['status'] = 1;
		$rs['picUrl'] = '/'.$savePath.$savePicName;
		
		$_u = new UserModel();
		$_u->where('id = '.$uid)->update(array('avatar' => $rs['picUrl']));
		print json_encode($rs);
		exit();
	}
}