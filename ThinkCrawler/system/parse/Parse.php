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
 * 解析类
 **/ 

class Parse{

    /**
     * 解析驱动
     *
     * @protected static $_driver 
     **/ 

    protected static $_driver = null;

    /**
     * 初始化函数 
     *
     * @access public 
     * @param  string $driver 驱动名称
     * @param  array  $option 实例化驱动时需要的参数 
     * @return void 
     **/ 

    public static function init($driver, $option){
        Loader::load('parse.driver.' . ucfirst(strtolower($driver)) . 'Driver');			
        $class    = ucfirst($driver)  . 'Driver';
        $instance = new ReflectionClass($class);
        self::$_driver = $instance->newInstanceArgs($option);
    }	

    /**
     * 解析函数 
     *
     * @access public static 
     * @param  string $path   要解析的文件路径 
     * @param  array  $ext    解析时需要的其他数据 
     * @return void 
     **/ 

    public static function fetch($path, $ext = array()){
        $driver = isset($ext['driver']) && $ext['driver'] ? $ext['driver'] : 'dom';
        $option = isset($ext['option']) && $ext['option'] ? $ext['option'] : array();
        self::init($driver, $option);
        self::$_driver->fetch($path, $ext);	
    } 

}
