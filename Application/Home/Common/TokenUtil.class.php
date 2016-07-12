<?php
namespace Home\Common;

class TokenUtil {
	/* 获取Token */
	static function fetchToken() {
		$access_token = S('access_token'); // 查询缓存中是否存在token项
		if(empty($access_token)) {
			S('access_token', WeixinChat::getAccessToken(), array('expire'=>60)); // 如果没有则获取最新的token存入缓存
		}
		return S('access_token'); // 返回得到的token
	}
}