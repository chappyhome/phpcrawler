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

//抓取server 

Loader::load('parse.Parse');

//声明全局变量
global $redis, $parser_server, $parser_monitor;

$config        = Loader::load_config('server');
$parser_server = new swoole_server($config['parser']['host'], $config['parser']['port']);

if(!$parser_server){
    exit('parser_server connect failed');	
}

$parser_server->set($config['parser']['options']);

//绑定事件及相应的回调函数

$parser_server->on('start', function(swoole_server $parser_server){
    echo 'parser_server start_time--' . date('Y-m-d H:i:s') . "\n";
    echo "master_pid:{$parser_server->master_pid}--manager_pid:{$parser_server->manager_pid}\n";	
    echo 'version--[' . SWOOLE_VERSION . "]\n";	
});

$parser_server->on('workerStart', function(swoole_server $parser_server, $worker_id){
    global $argv;
    if($worker_id >= $parser_server->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]} task worker");
    } else {
        swoole_set_process_name("php {$argv[0]} event worker");
    }
    echo 'workerStart time--' . date('Y-m-d H:i:s') . "\n";
    echo 'worker_id:' . $worker_id . "\n";
});

$parser_server->on('connect', function(swoole_server $parser_server, $fd, $from_id){
    echo 'client connect time--' . date('Y-m-d H:i:s') . "\n";
    echo 'client fd:' . $fd . "\n";
    echo 'client from_id:' . $from_id . "\n";
});


$parser_server->on('finish', function(swoole_server $parser_server, $data){
    echo 'async_task finish time--' . date('Y-m-d H:i:s') . "\n";
    echo 'connect_pid:' . posix_getpid().".\n";
});

$parser_server->on('close', function(swoole_server $parser_server, $fd, $from_id){
    echo 'client close time--' . date('Y-m-d H:i:s') . "\n";
    echo 'client fd:' . $fd . "\n";
    echo 'client from_id:' . $from_id . "\n";
});

$parser_server->on('workerStop', function(swoole_server $parser_server, $worker_id){
    echo 'workerStop time--' . date('Y-m-d H:i:s') . "\n";
    echo 'worker_id:' . $worker_id . "\n";
    echo 'pid:' . posix_getpid().".\n";
});

$parser_server->on('workerError', function(swoole_server $parser_server, $data){
    echo 'workerError time--' . date('Y-m-d H:i:s') . "\n";
    echo 'pid:' . posix_getpid().".\n";
});

$parser_server->on('shutdown', function(swoole_server $parser_server){
    echo 'server shutdown time--' . date('Y-m-d H:i:s') . "\n";
    echo 'server_pid:' . posix_getpid().".\n";
});

$parser_server->on('receive', function(swoole_server $parser_server, $fd, $from_id, $data){
    echo 'server receive time--' . date('Y-m-d H:i:s') . "\n";
    echo 'client fd:' . $fd . "\n";
    echo 'client--from_id:' . $from_id . "\n";
    $data = json_decode($data, true);
    $cmd  = $data['cmd'];
    unset($data['cmd']);
    switch($cmd){
    case 'fetch':
        $parser_server->task($data, 0);
        $parser_server->send($fd, "OK\n");
        break;
    default:
        echo "error cmd \n";
    }
});

$parser_server->on('task', function(swoole_server $parser_server, $task_id, $from_id, $data){
    echo 'task start time--' . date('Y-m-d H:i:s') . "\n";
    echo 'tast_id :' . $task_id. "\n";
    echo 'client--from_id:' . $from_id . "\n";
    $driver = isset($data['driver'])  ?  $data['driver'] : '';
    $method = isset($data['method'])  ?  $data['method'] : 'fetch';
    $object = isset($data['object'])  ?  unserialize($data['object']) : '';
    $class  = isset($data['class'])   ?  $data['class'] : 'Parse';
    if((!$class && !$object) || !$method){
        $crawler_server->finish("error callback\n");	
    }
    if($object){
        call_user_func_array(array($object, $method), array($data['data'], array('driver' => $driver)));
    }else{
        call_user_func_array("{$class}::{$method}", array($data['data'], array('driver' => $driver)));
    }
    $parser_server->finish("OK\n");	
});

//添加监控进程 
$parser_server->addProcess($parser_monitor);

//server启动
$parser_server->start();
