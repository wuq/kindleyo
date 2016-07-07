<?php
class FileController extends Controller{
	
	public function init(){
		$this->_user();
    }
	
	//文件下载
	public function downAction(){
		$url  = $_GET['u'];
		if(!$url) throw new Exception('请不要打开奇怪的东西');
		$uid  = 0;
		$ref  = $_SERVER['HTTP_REFERER'];
		$link = Common::encrypt($url, 'D', 'kindle');
		$key  = explode('|', $link);
		if(time() - $key[1] > 1800) throw new Exception('您的链接已经过期,请重新打开');
		if(false == strpos($ref, 'kindleyo.com')) throw new Exception('请不要使用盗链');
		if($i = Yaf_Session::getInstance()->get('u_id')) $uid = $i;
		$_bu  = new BookUrlModel();
		$book = $_bu->fRow($key[0]);	
		$this->booklog($book['book_id'], $uid, 1);
		$this->downloadFile($book['ulink'], $book['uname']);
	}
}