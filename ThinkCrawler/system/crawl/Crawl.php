<?php 
// +----------------------------------------------------------------------
// | ThinkCrawler Framework [ I CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011-2015 ThinkLei Team (http://www.smartlei.com)
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author ThinkLei <ThinkLei@126.com>
// +----------------------------------------------------------------------

/**
 * 抓取类 
 **/ 
class Crawl{

    /**
     * @protected static $_crawler
     *
     * 真正的抓取实例对象 
     **/ 

    protected static $_driver = null;

    /**
     * 初始化工作 
     *
     * @access public 
     * @param  string $driver 抓取类的名字 
     * @return void 
     **/ 

    public static function init($driver, $option = array()){
        Loader::load('crawl.driver.' . ucfirst(strtolower($driver)) . 'Driver');			
        $class     = ucfirst($driver)  . 'Driver';
        $instance  = new ReflectionClass($class);
        self::$_driver = $instance->newInstanceArgs($option) ;
    }	


    /**
     * 抓取数据
     * swoole_server->task 函数中调取  
     *
     * @access public  static 
     * @param  string  $url   要抓取的url 
     * @param  array   $ext   扩展数据 
     * @return void 
     **/ 

    public static function fetch($url, $ext = array()){
        //默认抓取为snoopy
        $driver = isset($ext['driver']) && $ext['driver'] ? $ext['driver'] : 'snoopy';
        $option = isset($ext['option']) && $ext['option'] ? $ext['option'] : array();
        self::init($driver, $option);
        self::$_driver->fetch($url, $ext);	
    } 
}
