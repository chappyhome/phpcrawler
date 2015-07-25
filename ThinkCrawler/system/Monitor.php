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
 * 调度中心 
 * 1、协调crawler和parser工作 
 * 2、由于swoole的特殊性，所有是回调函数的形式
 * 3、定义了监控器
 *
 **/ 

//定义一些全局变量
global $crawler_monitor, $parser_monitor, $redis, $crawler_server, $parser_server, 
	   $crawler_topic, $parser_topic, $start;

//加载site配置,里面包含了要抓取的网站列表及相应的配置,请看测试实例 

$site = Loader::load_config('site');

if(!$site && !isset($site['urls']) && !$site['urls']){
    exit('没有定义要抓取的网站列表');
}else{
    $urls = is_array($site['urls']) ? $site['urls'] : array($site['urls']);
}


//初始化redis 
$redis = init_redis();

$crawl_keys = $parse_keys = array();

//把要抓取的网站添加的要监控的队列
foreach($urls as $url){
	preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
	if(!$match){
        continue;
	}
	$key = md5(trim($match[0], '/'));

    /**
     * $start 参数
     * 1、因为本系统必须启动连个server才能完成想要的效果 crawer_server 和 parser_server
     * 2、每次启动server都会加载该文件
     * 3、避免重复放入队列(比如第一次在队列中时，我们的crawer_server已经抓取过了，如果重复放入，则会重复抓取)
     **/ 

	if($start){
		$redis->lpush($key, $url);
	}
	array_push($crawl_keys, $key);
	array_push($parse_keys, md5($key));
}

//无限循环时使用的key 不同的server使用不同的key

$crawler_topic = 'phpcrawlerkeys';
$parser_topic  = 'phpparserkeys';
$redis->set($crawler_topic, serialize($crawl_keys), 7*24*3600);
$redis->set($parser_topic, serialize($parse_keys), 7*24*3600);


//创建监控程序
for($i = 0; $i < 2; $i++){
	$crawler_monitor  = new swoole_process('crawler_monitor', false);    
	$parser_monitor   = new swoole_process('parser_monitor', false);    
}

