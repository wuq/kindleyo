<?php
class SubmitController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->keyword     = Common::keyword();
		$this->_layout->description = Common::description();
		$this->_layout->meta_title  = '图书投稿系统-kindleyo.com';
		$this->_layout->inc = ['j10','up', 'ke'];
		$this->_layout->mod = 'up';
		Common::checkLogin();
		Yaf_loader::import(APP_PATH.'library/qiniu/rs.php');
    }
	
	public function bookAction($id = 0){
		//error 1:标题空 2:封面空 3:描述空 4:ok
		$this->_view->setp = 1;
		$uid  = Yaf_Session::getInstance()->get('u_id');
		$_b   = new BookModel();
		$_c   = new CategoryModel();
		$cate = $_c->where('cate = 1')->fHash('id,cname');
		$task = $_b->where("user_id = '".$uid."' AND state != 2")->fRow();
		if($task && !$id){
			$this->_view->token = $this->qiniu_token(); 
			$this->_view->setp  = 2;
		}		
		if($this->http()->isPost()){
			$p = $_POST;
			$p['subject'] || exit('1');
			$p['cover'] || exit('2');
			$p['des'] || exit('3');
			$p['user_id'] = $uid;
			$p['state']   = 1;
			$p['cdate']   = date('Y-m-d H:i:s');

			($p['id'] && $_b->where('id = '.$p['id'])->update($p)) && exit('4');
			($_b->insert($p)) && exit('4');
			throw new Exception('没有填写成功');
		}
		$this->_view->id   = $task['id'];
		$this->_view->find = $task;
		$this->_view->cate = $cate;
	}
	
	public function animeAction(){
		
		
	}
	
	
	//上传封面
	public function coverAction(){
		if(getimagesize($_FILES['file']['tmp_name'])){
			$e    = explode('.', $_FILES['file']['name']);
			$path = 'cover'.DS.date('Ymd');
			is_dir($path) || mkdir($path);
			$file = $path.DS.uniqid().'.'.end($e);
			$img  = new Image($_FILES['file']['tmp_name'], '1', '110', '164', $file);
			$img->outimage();
			echo DS.$file;exit(0);
		}
		exit('ERR');
	}
	
	//文件名发送到服务端
	public function fileAction(){
		//error 1:书名空 2:书ID空 3:ok 4:err
		if($this->http()->isPost()){
			$p   = $_POST;
			$p['uname'] || exit('un');
			$p['x:book_id'] || exit('bid');
			$p['book_id'] = $p['x:book_id'];
			$_bu = new BookUrlModel();
			($_bu->ffBook_id($p['book_id'])) && exit('ok');
			($_bu->insert($p)) && exit('ok');
			exit('err');
		}
	}
	
	//回调
	public function  callAction(){
		$retStr  = $_GET["upload_ret"];
		$errCode = $_GET["code"];
		$errMsg  = urldecode($_GET["error"]);       
		if($retStr){
			$decodedRet = base64_decode($retStr);
			$retObj = json_decode($decodedRet);   
			$picKey = $retObj->{"key"};
			$bookId = $retObj->{"x:book_id"};
			Qiniu_SetKeys(ACCESSKEY, SECRETKEY);
			$baseUrl    = Qiniu_RS_MakeBaseUrl(DOMAIN, $picKey);
			$getPolicy  = new Qiniu_RS_GetPolicy();
			$privateUrl = @$getPolicy->MakeRequest($baseUrl);
			$_b  = new BookModel();
			$_b->where('id = '.$bookId)->update(['state' => 2]);
			$_bu = new BookUrlModel();
			$_bu->where('book_id = '.$bookId)->update(['ulink' => $privateUrl, 'state' => 2]);
			$uid = Yaf_Session::getInstance()->get('u_id');
			$this->booklog($bookId, $uid, 3);
			$this->redirect('/submit/call');
		}
		if(!$retStr){
			$this->_view->errcode = $errCode;
			$this->_view->errmsg  = $errMsg;
		}
	}
	
}
