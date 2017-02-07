<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

function get_token() 
{
        if(file_exists("token.txt"))
        {
            $token=fopen("token.txt","r");
            $access_token=fread($token,filesize("token.txt"));
            $access_token=json_decode($access_token);
            if(intval(time())-intval($access_token->time)<=7000) {return $access_token->access_token;}
            
        }

        $ch=curl_init();

        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx81c1603b41b5f4f6&secret=15d9382d85bb018c56e3cc41bb299d5b";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , 0);

        $access_token=curl_exec($ch);
        $access_token=json_decode($access_token);


        curl_close($ch);

        $access_token->time=time();
        $token=fopen("token.txt","w");
        fwrite($token,json_encode($access_token));
        fclose($token);
        return $access_token->access_token;
}
        

        $access_token = get_token();


        $url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$_POST['mediaId'];
        //获取微信“获取临时素材”接口返回来的内容（即刚上传的图片）  
        $a = file_get_contents($url);  
        echo 'data:image/jpg;base64,'.chunk_split(base64_encode($a));
?>