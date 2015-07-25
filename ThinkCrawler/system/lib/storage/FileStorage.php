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

Loader::load('lib.storage.Storage');

/**
 * 文件存储
 **/ 

class FileStorage extends Storage{

    /**
     * 存储函数 
     *
     * @access public 
     * @param  string $url 
     * @param  string $data 
     * @return void 
     **/ 

    public function save($url, $data){
        if(!$url || !$data){
            return false;
        }
        $url = trim($url, '/');
        $first = stripos($url, '/');
        $end   = strripos($url, '/'); 
        $tmp_url = $url;
        $last = 'index';
        if(!($first == $end || $first == ($end-1))){
            $last = strrchr($url, '/');
            if($last && strpos($last, '.')){
                $tmp_url = substr($url, 0, $end);
            }
        }
        preg_match('/http:\/\/([^\/]+)[\/]?([a-zA-Z0-9\/]*)/i', $tmp_url, $match);
        if(!$match){
            return false;	
        }
        $store = Loader::load_config('store');
        $sub   = isset($match[2]) && $match[2] ? $match[2] : '';
        $sub   = $sub ? trim($sub, '/') . '/' : '';
        $path  = $store['save_path'] . trim($match[1], '/') . '/' . $sub;	
        $file  = $path . trim($last, '/'); 
        $content = is_object($data) ? $data->results : $data;
        check_path($path, 0777);
        @file_put_contents($file, serialize($content));

        //务必调用否则不能触发解析程序
        push_to_parser($url, $file);
    }

}
