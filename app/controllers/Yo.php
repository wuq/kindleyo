<?php
class YoController extends Controller{
	
	private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
		$this->_layout->meta_title  = '看雨阅读-kindleyo.com';
		$this->_layout->keyword     = Common::keyword();
		$this->_layout->description = Common::description();
		$this->_layout->inc = [];
		$this->_layout->mod = 'book';
		$this->_layout->url = $this->http()->getRequesturi();
    }
	
	public function aboutAction(){
		
	}
	
	
	public function disclaimerAction(){
	
	}
	
	public function advertAction(){
	
	}
	
	public function disseminateAction(){
	
	}
}