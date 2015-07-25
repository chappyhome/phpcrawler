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
 * 规范抓取类,抓取驱动 
 *
 **/ 

abstract class Driver{

    /**
     * @var public $storage 
     *
     * 存取抓取内容的实例 
     **/ 

    public $storage = null;

    /**
     * @var public $engine
     *
     * 抓取实例(真正进行抓取工作的实例) 
     **/ 

    public $engine  = null;

    public function __construct(Storage $storage, $engine){
        $this->storage = $storage;	
        $this->engine  = $engine;
    }

    abstract function fetch($url, $ext = array());

    /**
     * 判断是否有用户自定义的业务逻辑
     **/ 

    public function custom_fetch($url, $ext){
        $hooks = Loader::load_config('hooks', false);
        if($hooks && isset($hooks['crawl'])){
            Loader::load('crawl.' . strtolower($hooks['crawl']['class']));
            $obj  = new $hooks['crawl']['class'];	
            $args = array($this, $url, $ext);
            call_user_func_array(array($obj, $hooks['crawl']['method']), $args);
            return true;
        }
        return false;
    }
}
