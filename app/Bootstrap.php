<?php 

define('BUCKET', 'kindleyo');
define('ACCESSKEY', 'Pt6X-WscgZiXy77roMFZbwJ18zqqfOQUrO_FM9S');
define('SECRETKEY', '5dgUj0t5UZMWUBGc5D_Xt2M68PcBviGGA1UJtDl');
define('DOMAIN', 'kindleyo.qiniudn.com');
define('HOST', 'www.kindleyo.com');

class Bootstrap extends Yaf_Bootstrap_Abstract {

	public function _initBootstrap(){
		Yaf_Session::getInstance()->start();

	}

    
	public function _initConfig(){
		$config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set("config", $config);
	}

    public function _initLayout(Yaf_Dispatcher $dispatcher){
		//main
		/*layout allows boilerplate HTML to live in /views/layout rather than every script*/
        $main = new LayoutPlugin('layout.phtml');

        /* Store a reference in the registry so values can be set later.
         * This is a hack to make up for the lack of a getPlugin
         * method in the dispatcher.
         */
        Yaf_Registry::set('layout', $main);

        /*add the plugin to the dispatcher*/
        $dispatcher->registerPlugin($main);
		
    }


}
