<?php
if (!isset($_GET['state'])) $_GET['state'] = 'index';
$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx81c1603b41b5f4f6&redirect_uri=http%3A%2F%2Fwap.xszlv.com%2Fwx&response_type=code&scope=snsapi_userinfo&state=$_GET[state]#wechat_redirect";
header ('Location:' . $url);
