<?php
namespace Home\Common;

class WeixinChat {
	static function getAccessToken() {
		$appid = C('appid');
		$appsecret = C('appsecret');
		
		$url = 'https://api.weixin.qq.com/cgi-bin/token';
		
		$params = array();
		$params['grant_type'] = 'client_credential';
		$params['appid'] = $appid;
		$params['secret'] = $appsecret;
		
		$httpstr = http($url, $params);
		
		$harr = json_decode($httpstr, true);
		return $harr['access_token'];
	}
	
	static function createMenu() {
		$jasondata = '{
    "button": [
        {
            "name": "进入商城", 
            "type": "view", 
            "url": "http://z149u31399.51mypc.cn/wechatshop/"
        }
    ]
}';
		$access_token = TokenUtil::fetchToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		$menustr = http($url, $jasondata, 'POST',array("Content-type: text/html; charset=utf-8"),true);
		//print_r($menustr);
		return $menustr;
	}	
}