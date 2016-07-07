<?php
class IndexController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = Yaf_Session::getInstance()->get('u_nick');
		$this->_layout->keyword     = Yaf_Session::getInstance()->get('u_nick').'的书单';
		$this->_layout->description = Yaf_Session::getInstance()->get('u_nick').'的书单';
		$this->_layout->mod = 'book';
		$this->_layout->inc = array('j10');
		$this->_layout->url = $this->http()->getRequesturi();		
    }
	
	public function indexAction($id = 0){
		$id || $id = Yaf_Session::getInstance()->get('u_id');
		if(!$id) throw new Exception('403');
		$_u = new UserModel();
		$_b = new BookModel();
		
		$fb  = $_b->where('user_id = '.$id)->limit('10')->fList();
		$fbc = $_b->where('user_id = '.$id)->fOne('count(*)');
		
		$this->_layout->me = $_u->fRow($id);
		$this->_view->fb   = $fb;
		$this->_view->fbc  = $fbc;
		
	}
}