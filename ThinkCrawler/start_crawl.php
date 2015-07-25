<?php 
global $start;
$start = true;
include 'constants.php';
include CORE_PATH. 'Loader.php';
Loader::load('helpers.dom');
Loader::load('helpers.common');
Loader::load('Monitor');
Loader::load('server.crawler_server');
