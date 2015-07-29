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
 * 循环检测目录是否存在，如不存在则创建
 *
 * @param  string $path 目录 
 * @param  int     $mode  目录权限
 * @return boolean
 **/ 

if(!function_exists('check_path')){
    function check_path($path, $mode = 0755){
        if(!is_dir($path)){
            check_path(dirname($path), $mode);
        }
        return @mkdir($path, $mode);
    }
}

/**
 * 初始化redis并连接
 *
 * @param  void 
 * @return resource $redis
 **/ 

if(!function_exists('ini_redis')){
    function init_redis($config = array()){
		static $_redis = array();
		$key = !$config ? md5('config') : md5(implode('', $config));
		if(isset($_redis[$key]) && $_redis[$key]){
			return $_redis[$key];	
		}
        //加载redis配置
        $config = $config ? $config : Loader::load_config('redis');
        $redis  = new Redis();
        $redis->connect($config['host'], $config['port']);
		$_redis[$key] = $redis;
        return $redis;
    }
}

/**
 * 抓取服务的监控进程的回调函数
 *
 * @param  object swoole_process $worker
 * @return void
 **/ 

if(!function_exists('crawler_monitor')){
    function crawler_monitor(swoole_process $worker){
        global $redis, $crawler_server, $crawler_topic;

        //无限循环，如果在相应的队列拿到数据就执行抓取工作
        while(true){
            $keys = $redis->get($crawler_topic);
            if(!$keys){
                continue;
            }
            $keys = unserialize($keys);
            foreach($keys as $key){
                if($url = $redis->rpop($key)){
                    echo "开始抓取链接@@{$url}的内容\n";
                    $site  = Loader::load_config('site');
                    $data  = array(
                        'driver' => isset($site['conf'], $site['conf'][$key]['crawler']) ? $site['conf'][$key]['crawler'] : 'snoopy',
                        'data'   => $url, 
                    );
                    $crawler_server->task($data, 0);
                }
            }
            sleep(5);
        }
    }
}

/**
 * 解析服务的监控进程的回调函数 
 *
 * @param  object swoole_process $worker
 * @return void
 **/ 

if(!function_exists('parser_monitor')){
    function parser_monitor(swoole_process $worker){
        global $redis, $parser_server, $parser_topic;

        //无线循环，如果在相应的队列拿到数据就执行分析工作
        while(true){
            $keys = $redis->get($parser_topic);
            if(!$keys){
                continue;
            }
            $keys = unserialize($keys);
            foreach($keys as $key){
                if($path = $redis->rpop($key)){
                    echo "开始解析文件@@{$path}\n";
                    $site = Loader::load_config('site');
                    $data = array(
                        'driver' => isset($site['conf'], $site['conf'][$key]['parser']) ? $site['conf'][$key]['parser'] : 'dom',
                        'data'   => $path, 
                    );
                    $parser_server->task($data, 0);
                }
            }
            sleep(5);
        }
    }
}

/**
 * 把生成的文件存储位置到parser中相应的队列 
 *
 * @param  $url  抓取的url 
 * @param  $path 文件存储的位置 
 * @return void
 **/

if(!function_exists('push_to_parser')){
    function push_to_parser($url, $path){
        global $redis; 
        preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
        if(!$match){
            return ;
        }
        $key = md5(md5(trim($match[0], '/')));

        //防止重复放入
        $key_exist = 'hsetparse';

        if($redis->hget($key_exist, md5($url))){
            return; 
        }
        $redis->hset($key_exist, md5($url), 1);
        $redis->lpush($key, $path);
    }
}

/**
 * 把从文件中解析出来的url放到抓取队列中
 *
 * @param  string $url 解析出的的url 
 * @return void 
 **/

if(!function_exists('push_to_crawler')){
    function push_to_crawler($url){
        global $redis; 
        preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
        if(!$match){
            return;
        }
        $key = md5(trim($match[0], '/'));

        //防止重复放入
        $key_exist = 'hsetcrawl';
        if($redis->hget($key_exist, md5($url))){
            return; 
        }
        $redis->lpush($key, $url);
        $redis->hset($key_exist, md5($url), 1);
    }
}

/**
 *
 **/
if(!function_exists('host_to_ip')){
	function host_to_ip($host){
		$redis = init_redis();	
		$key   = 'dnscachehost_to_ip';
		$ips   = array();
		if(!($ips = $redis->hget($key, md5($host)))){
			Loader::load('lib.dns.DNSRecord');
			$records = DNSRecord::get_records($host);	
			if($records){
				$ips = serialize(array_column($records, 'ip'));	
			}
		}
		if($ips){
			$redis->hset($key, md5($host), $ips);
			$ips = unserialize($ips);
			return $ips[rand(0, intval(count($ips) - 1))];
		}
	}
}













if(!function_exists('parser_monitor')){
    function my_mysql_query($sql){
        $db = init_db();
        $result = mysql_query($sql, $db);
        $data = array();
        if($result){
            while($row = mysql_fetch_assoc($result)){
                $data[]  = $row;
            }
        }
        return $data;
    }
}

if(!function_exists('parser_monitor')){
    function my_mysql_insert($sql){
        $db = init_db();
        $result = mysql_query($sql, $db);
        return mysql_insert_id($db);
    }
}

if(!function_exists('parser_monitor')){
    function init_db($config = array()){
        $config = empty($config) ? Loader::load_config('db') : $config;
        if(empty($config)){
            exit('db config is empty');
        }
        static $_db = null;
        if($_db){
            return $_db;
        }
        ($_db = mysql_connect($config['host'], $config['port'])) or die('Could not connect to mysql server.');
        mysql_select_db($config['dbname'], $_db) or die('Could not select database.');
        mysql_query("SET NAMES utf8", $_db);
        return $_db;
    }
}
