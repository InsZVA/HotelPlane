<?php
$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx81c1603b41b5f4f6&redirect_uri=http%3A%2F%2Fwap.xszlv.com%2Fwx&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
echo "<script>window.location.replace('$url')</script>";
