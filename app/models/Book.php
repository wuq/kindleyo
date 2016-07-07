<?php

class BookModel extends Mysql{
	
	public $table	= 'book';
	public $pk 		= 'id';
	
	/*
	id	书id
	d	下载
	v	查看
	c	评论
	*/
	public function nums($id = 0, $d = 0, $v = 0, $c = 0){
		$str = '';
		if($d){
			$str .= ' `downs` = `downs` + 1,';
		}
		if($v){
			$str .= ' `views` = `views` + 1,';
		}
		if($c){
			$str .= ' `cmts` = `cmts` + 1,';
		}
		$str = substr($str, 0, -1);
		$sql = "UPDATE `book` SET".$str.' WHERE `id` = '.$id;
		$this->query($sql);
	}
	
	//下载排行
	public function downTop(){
		$sql = "SELECT COUNT(book_id) AS num, b.`subject`, b.id FROM booklog AS l LEFT JOIN book AS b ON l.book_id = b.id WHERE l.ltext = '下载'  GROUP BY l.book_id DESC LIMIT 5"; // ORDER BY l.ldate
		return $this->query($sql);
	}
	
	//图书统计
	public function countBook($str = ''){
		$where  = '*';
		if($str == 'num'){$where = 'COUNT(id) AS num';}
		if($str == 'down'){$where = 'SUM(downs) AS down';}
		$sql = "SELECT {$where} FROM book";
		$res = $this->query($sql);
		return $res[0][$str];
	}
}