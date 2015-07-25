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
 * 解析驱动抽象类 
 **/ 

abstract class Driver{

    /**
     * @var public $storage 存储解析内容的实例
     **/  
    public $storage = null;

    /**
     * @var public $engine 
     *
     * 解析引擎 真正的解析类 
     **/ 

    public $engine  = null;

    public function __construct(Storage $storage, $engine){
        $this->storage = $storage;
        $this->engine  = $engine;
    }

    abstract function fetch($data, $ext = array());

    /**
     * 判断是否有用户自定义的业务逻辑
     **/ 

    public function custom_fetch($path, $ext){
        $hooks = Loader::load_config('hooks', false);
        if($hooks && $hooks['parse']){
            Loader::load('parse.' . strtolower($hooks['parse']['class']));
            $obj  = new $hooks['parse']['class'];	
            $args = array($this, $path, $ext);
            call_user_func_array(array($obj, $hooks['parse']['method']), $args);
        }
    }
}
