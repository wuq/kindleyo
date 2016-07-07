<?php
/*
 *lygus0@gmail.com
 *控制器基类
 *2014年7月29日
*/
class Controller extends Yaf_Controller_Abstract{
	
    	
	//用户
	public function _user(){
		$_u = new UserModel();
		$GLOBALS['USER']   = $_u->fHash('id,nick');
		$GLOBALS['AVATAR'] = $_u->fHash('id,avatar');
	}
	
	public function _cate(){
		$_c = new CategoryModel();
		$GLOBALS['CATE'] = $_c->where('cate = 1')->fHash('id,cname');
	}

	//返回分类的 ID, 标签
	public function cate($t = 'name'){
		$_c = new CategoryModel();
		$hash = $t == 'name'? 'ctag,cname' : 'ctag,id';
		$res  = $_c->where('cate = 1')->fHash($hash);
		$res['new']  = '最新';
		$res['more'] = '更多推荐';
		return $res;
	}
	
	//http实例
	public function http(){
		$http = new Yaf_Request_Http();
		return $http;
	}
	
	//上传凭证
	public function qiniu_token(){
		Qiniu_SetKeys(ACCESSKEY, SECRETKEY);
		$putPolicy = new Qiniu_RS_PutPolicy(BUCKET);
		$putPolicy->ReturnUrl = 'http://'.HOST.'/submit/call';
		$upToken   = $putPolicy->Token(null);
		return $upToken;
	}
	
	
	//下载
	public function downloadFile($file, $name){
        header('Pragma: public');       // required 
        header('Expires: 0');           // no cache 
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
        header('Cache-Control: private',false); 
        header('Content-Type: application/force-download'); 
        header('Content-Disposition: attachment; filename="'.$name.'"'); 
        header('Content-Transfer-Encoding: binary'); 
        header('Connection: close'); 
        readfile($file);				// push it out 
        exit(); 
	}
	
	//书日志
	//type 1:下载 2:评论 3:上传
	public  function booklog($bookid = 0, $userid = 0, $type = 0){
		$_bl = new BookLogModel();
		$_b  = new BookModel();
		switch($type){
			case 1 : $log = '下载'; break;
			case 2 : $log = '评论'; break;
			case 3 : $log = '上传'; break;
		}
		$_bl->insert([
			'book_id' => $bookid,
			'user_id' => $userid,
			'ltext'   => $log,
			'ldate'   => date('Y-m-d H:i:s')
		]);
		if($type == 1) $_b->nums($bookid, 1); //下载
		if($type == 2) $_b->nums($bookid, 0, 0, 1); //评论
		
	}
	
	
}