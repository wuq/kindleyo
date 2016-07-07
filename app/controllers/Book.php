<?php
class BookController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = '看雨阅读-kindleyo.com';
		$this->_layout->keyword     = Common::keyword();
		$this->_layout->description = Common::description();
		$this->_layout->inc = ['j10', 'sti'];
		$this->_layout->mod = 'book';
		$this->_layout->url = $this->http()->getRequesturi();
		$this->_user();
		$this->_cate();
    }
	
	//列表
	public function listAction($cate = '', $p=1){
		$cate || $cate = 'more';
		$cname = $this->cate('name');
		$cid   = $this->cate('id');
		if(!isset($cname[$cate])) throw new Exception('请不要试图访问一些乱七八糟的');
		$this->_layout->cate 		= $cname[$cate];
		$this->_layout->meta_title  = $cname[$cate].'_kindle-kindleyo.com';
		$this->_layout->keyword		= $cname[$cate].'下载,电子书,kindle,mobi';
		$_b = new BookModel();
		
		$off 	= 40;	
		$start  = $p - 1;
		$begin  = ceil($start * $off);
		$limit  = "{$begin}, {$off}";
		if($cate == "new"){
			$where = 'state = 2';
			$order = 'cdate DESC';
		}elseif($cate == "more"){
			$where = 'state = 2';
			$order = 'cdate DESC, views DESC';
		}else{			
			$where = 'state = 2 AND cate_id = '.$cid[$cate];
			$order = 'cdate DESC';
		}
		$find   = $_b->where($where)->order($order)->limit($limit)->fList();
		$count  = $_b->where($where)->fOne('count(*)');
		$params = [
			'total_rows'=>$count,
			'method'    =>'html', 
			'parameter' =>'/book/list/cate/'.$cate.'/p/?',  
			'now_page'  =>$p, 
			'list_rows' =>$off,
		];
		$page = new Page($params);
		$this->_view->find = $find;
		$this->_view->page = $page->show();
	}
	
	//详细
	public function viewAction($id = 0){
		if(!$id) throw new Exception('参数错误');	
		$_b  = new BookModel();
		$_b->nums($id, 0, 1, 0);
		$_bc = new BookCommentModel();
		$_bu = new BookUrlModel();
		$cmts = $_bc->where('book_id = '.$id)->order('id DESC')->limit('10')->fList();
		$link = $_bu->where('book_id = '.$id)->fList();
		$find = $_b->fRow($id);	
		$more = $_b->query('SELECT * FROM book WHERE `user_id` = '.$find['user_id'].'  ORDER BY RAND() LIMIT 10');
		$this->_layout->link = $link;
		$this->_layout->cate = $find['subject'];
		$this->_layout->bid  = $id;
		$this->_layout->meta_title  = $find['subject'].'_kindle-kindleyo.com';
		$this->_layout->keyword     = $find['subject'].',mobi,kindle,电子书';
		$this->_layout->description = $find['subject'].'下载,介绍';
		$this->_view->find   = $find;
		$this->_view->more   = $more;
		$this->_view->cmts   = $cmts;
	}
	
	//评论
	public function commentAction(){
		//error 1:未登录 2:book_id为空 3:评论为空 4:重复 5:ok 6:err
		$uid = Yaf_Session::getInstance()->get('u_id');
		$uid ||	exit('1');
		
		if($this->http()->isPost()){
			$po  = $_POST;
			$po['book'] || exit('2');
			$po['cmt'] || exit('3');
			$_bc = new BookCommentModel();
			$tm  = $_bc->where('book_id = '.$po['book'].' AND user_id = '.$uid)->order('id DESC')->fOne('cdate');
			if($tm) (time() - strtotime($tm) > 120) || exit('4');
			
			$s = $_bc->insert([
				'book_id' => $po['book'],
				'user_id' => $uid,
				'cmt'	  => htmlspecialchars($po['cmt'], ENT_QUOTES),
				'cdate'	  => date('Y-m-d H:i:s')
			]);
			echo $s? '5' : '6';
			exit();
		}
	}
	
}