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

Loader::load('crawl.driver.Driver');
Loader::load('lib.crawler.Snoopy');
Loader::load('lib.storage.FileStorage');
Loader::load('lib.dns.DNSRecord');

class SnoopyDriver extends Driver{

    /**
     * 构造函数
     **/ 

    public function __construct(){
        parent::__construct(new FileStorage(), new Snoopy());
    }

    /**
     * 抓取函数
     *
     * @access public 
     * @param  string $url 要抓取的url 
     * @param  array  $ext 抓取所需的其他数据 
     * @return void
     **/ 

    public function fetch($url, $ext = array()){
        $res = $this->custom_fetch($url, $ext);
        if($res){
            return;
        }
        if(!$url || !preg_match('/http[s]?:\/\/[[A-Za-z0-9_?.%&=\/#@!]*/i', $url)){
            continue;
        }	
		$arr  = parse_url($url);
		$host = $arr['scheme'] . '://' . $arr['host'] . (isset($arr['port']) ? ':' . $arr['port'] : '');
		$ip   = 'http://' . host_to_ip($host);
		var_dump($ip);
		$url  = str_replace($host, $ip, $url);
        $this->engine->fetch($url);	
        if($this->engine && $this->engine->status == 200){
            $this->storage->save($url, $this->engine);	
        }
    }
}
