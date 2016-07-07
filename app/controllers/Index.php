<?php
class IndexController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = '看雨阅读-kindleyo.com';
		$this->_layout->keyword     = Common::keyword();
		$this->_layout->description = Common::description();
		$this->_layout->inc = ['j10','sti'];
		$this->_layout->mod = 'book';
		$this->_layout->url = $this->http()->getRequesturi();
		$this->_user();
    }
	
	public function indexAction(){
		$_b = new BookModel();
		//最新
		$zx = $_b->where('state = 2')->order('cdate DESC')->limit('10')->fList();
		//推荐
		$tj = $_b->where('state = 2 AND views > 10 AND views < 100')->order('views ASC')->limit('6')->fList();

		$this->_view->zx = $zx;
		$this->_view->tj = $tj;
	}
	
}