<?php 
/**
 * 要抓取的url列表 
 * 1、一定要那下面的格式定义，否则会出现错误。
 * 2、urls数据里面的值不要带最后的 ‘/’
 * 3、保证conf里面md5的值是要抓取的urls的scheme+host,没有'/',即使你的抓取并不适合从网站的首页开始抓取
 **/ 

return array(
    'urls' => array(
        'http://www.baidu.com', 
        'http://www.qqgexingqianming.com', 
    ),

    'conf' => array(
        // crawler配置 
        
        md5('http://www.baidu.com') => array(
            //使用snoopy抓取
            'crawler' => 'snoopy',
        ), 
        md5('http://www.qqgexingqianming.com') => array(
            'crawler' => 'snoopy',
        ), 

        //parser配置
        md5(md5('http://www.baidu.com')) => array(
            //使用dom解析
            'parser'  => 'dom', 
        ), 
        md5(md5('http://www.qqgexingqianming.com')) => array(
            'parser'  => 'dom', 
        ), 
    ),
);

