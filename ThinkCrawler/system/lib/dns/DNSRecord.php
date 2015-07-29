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

class DNSRecord{
	
	/**
	 * var $_type 
	 * dns查询类型，具体参见手册
	 *
	 * 只提供常用的查询
	 **/ 

	private static $_type = array(
		'DNS_A', 'DNS_CNAME', 'DNS_MX', 'DNS_NS', 'DNS_SOA', 'DNS_AAAA', 'DNS_A6', 'DNS_ALL', 'DNS_ANY'	
	);

	/**
	 * 获得dns记录
	 *
	 * @access public static 
	 * @param  string $domain 
	 * @param  $type  DNS查询类型 
	 * @return array
	 **/

	public static function get_records($domain, $type = DNS_ALL){
		$result  = array();
		$type    = in_array($type, self::$_type) ? $type : DNS_ALL;
		$records = dns_get_record($domain, $type);
		if($records){
			foreach($records as $record) {
				if(isset($record['ip'])){
					$result[] = $record;
				}
			}
		}
		return $result;
	}

}

