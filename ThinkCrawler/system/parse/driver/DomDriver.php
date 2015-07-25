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

Loader::load('parse.driver.Driver');
Loader::load('lib.parser.Dom');
Loader::load('lib.storage.FileStorage');

class DomDriver extends Driver{

    public function __construct(){
        parent::__construct(new FileStorage(), new DOM());
    }

    /**
     * 解析函数 
     *
     * @access public 
     * @param  string  $path 文件路径 
     * @param  array   $ext  其他数据 
     * @return void 
     **/ 

    public function fetch($path, $ext = array()){
        //检测是否有用户自定义的逻辑 
        //具体的业务逻辑是由用户自己定义的

        $this->custom_fetch($path, $ext);
        return;
    }
}
