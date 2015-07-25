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

global $start;
$start = true;
include 'constants.php';
include CORE_PATH. 'Loader.php';
Loader::load('helpers.dom');
Loader::load('helpers.common');
Loader::load('Monitor');
Loader::load('server.crawler_server');
