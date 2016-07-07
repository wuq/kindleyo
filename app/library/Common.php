<?php
class Common{
	
	//是否登录
	public static function checkLogin(){
		if(!Yaf_Session::getInstance()->get('u_id')){
			static::redirect('/user/login');
		}
	}
	
	//是否管理员
	public static function isAdmin(){
		if(!$_SESSION['admin']){
			static::unSession();
			throw new exception('403 木有权限!');
		}
	}

	
	//跳转
	public static function redirect($url = ''){
		echo "<script>window.location.href='" . $url . "'</script>";
		exit();
	}
	
	public static function keyword(){
		return '看雨阅读,kindle 电子书,kindle,mobi,kindle社区, kindleyo';		
	}
	
	public static function description(){
		return '做体验最好的kindle电子书分享网站';		
	}
	
	
	
	//加密
	public static function encrypt($string = '', $operation = '', $key=''){
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D'? base64_decode($string):substr(md5($string.$key) ,0 ,8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = [];
        $result = '';
        for($i = 0; $i <= 255; $i ++){
            $rndkey[$i] = ord($key[$i%$key_length]);
            $box[$i]    = $i;
        }
        for($j = $i = 0; $i < 256; $i ++){
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i ++){
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'D'){
            if(substr($result, 0, 8) == substr(md5(substr($result, 8) . $key) ,0 ,8)){
                return substr($result, 8);
            }else{
                return '';
            }
        }else{
            return str_replace('=' ,'', base64_encode($result));
        }
    }
	
	
	
	//右侧标签列
	public static function category($type = 1){
		$_c = new CategoryModel();
		return $_c->where('cate = '.$type)->fList();
	}
	
	//右侧日志列
	public static function booklog($bid = 0){
		$_bl = new BookLogModel();
		//.' AND user_id != 0'
		$log = $_bl->where('book_id = '.$bid)->limit('5')->fList();
		return $log;
	}
	
	//右侧下载列
	public static function downTop(){
		$_b = new BookModel();
		return $_b->downTop();
	}
	
	public static function recommend($cate = ''){
		$_c = new CategoryModel();
		$_b = new BookModel();
		$cid = $_c->where("cname = '".$cate."'")->fOne('id');
		if($cate == '更多推荐' || $cate == '最新'){
			$res = $_b->order('views DESC')->limit('8')->fList();
		}else{
			$res = $_b->where('cate_id ='.$cid)->order('views DESC')->limit('8')->fList();
		}
		return $res;
	}
	
	//右侧统计
	public static function countBook($str = ''){
		$_b = new BookModel();
		return $_b->countBook($str);
	}
	
	/**
	 * int type:: 1:guest 2:spider
	 * str spider:: null
	 * 
	 */
	public static function accessLog(){
		$al = new AccessLogModel();
		$ip = new Ip();
		$type 	= 1;
		$spider = 'null';
		$ipaddr	= self::getIp();
		$addr 	= $ip->ip2addr($ipaddr);
		$date	= date('Y-m-d');
		if(self::is_spider()){
			$spider  = self::robot();
			$type = 2;
		}
		$data = [
				'ip'		=> $ipaddr,
				'type'		=> $type,
				'spider'	=> $spider,
				'num'		=> 1,
				'country'	=> $addr['country'],
				'area'		=> $addr['area'],
				'cdate' 	=> $date,
		];
		$find = $al->where("ip = '".$ipaddr."' AND cdate = '".$date."'")->fRow();
		if($find){
			$data['num']   = $find['num'] + 1;
			$al->where('id ='.$find['id'])->update($data);
		}else{
			$al->insert($data);
		}		
	}
	
	
	public static function is_spider(){
		$is_bot = 0;
		$USER_AGENT = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($USER_AGENT,"bot")) $is_bot = 1;
		if(strpos($USER_AGENT,"spider")) $is_bot = 1;
		if(strpos($USER_AGENT,"slurp")) $is_bot = 1;
		if(strpos($USER_AGENT,"mediapartners-google")) $is_bot = 1;
		if(strpos($USER_AGENT,"fast-webcrawler")) $is_bot = 1;
		if(strpos($USER_AGENT,"altavista")) $is_bot = 1;
		if(strpos($USER_AGENT,"ia_archiver")) $is_bot = 1;
		return $is_bot;
	} 
	
	public static function robot(){
		$tmp = $_SERVER['HTTP_USER_AGENT'];
		$bot = 'spider';
		if(strpos($tmp, 'Googlebot') !== false) $bot =  '谷歌';
		if(strpos($tmp, 'Baiduspider') >0) $bot = '百度';
		if(strpos($tmp, 'Yahoo! Slurp') !== false) $bot = '雅虎';		
		if(strpos($tmp, 'msnbot') !== false) $bot = 'Msn';			
		if(strpos($tmp, 'Sosospider') !== false) $bot = '搜搜';
		if(strpos($tmp, 'YodaoBot') !== false || strpos($tmp, 'OutfoxBot') !== false) $bot = '有道';	
		if(strpos($tmp, 'Sogou web spider') !== false || strpos($tmp, 'Sogou Orion spider') !== false) $bot = '搜狗';	
		if(strpos($tmp, 'fast-webcrawler') !== false) $bot = 'Alltheweb';			
		if(strpos($tmp, 'Gaisbot') !== false) $bot = 'Gais';			
		if(strpos($tmp, 'ia_archiver') !== false) $bot = 'Alexa';			
		if(strpos($tmp, 'altavista') !== false) $bot = 'AltaVista';			
		if(strpos($tmp, 'lycos_spider') !== false) $bot = 'Lycos';			
		if(strpos($tmp, 'Inktomi slurp') !== false) $bot = 'Inktomi';		
		return $bot;
	}
	
	public static function getIp(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		  $cip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		  $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}elseif(!empty($_SERVER["REMOTE_ADDR"])){
		  $cip = $_SERVER["REMOTE_ADDR"];
		}else{
		  $cip = "0.0.0.0";
		}
		return $cip;
	}
}