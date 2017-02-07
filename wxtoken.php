<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
require_once (__DIR__ . "/../Weixin/Weixin.php");
require_once (__DIR__ . "/../Config/MySQL.php");

function get_token() 
{
        /*if(file_exists("token.txt"))
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

        $access_token=curl_exec($ch);
        $access_token=json_decode($access_token);


        curl_close($ch);

        $access_token->time=time();
        $token=fopen("token.txt","w");
        fwrite($token,json_encode($access_token));
        fclose($token);*/
        //return $access_token->access_token;
        $wx = new Weixin();
        return $wx->getToken();
}
        

        $access_token = get_token();

        $ch=curl_init();

        $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , 0);

        $jsapi_ticket = json_decode(curl_exec($ch));

        curl_close($ch);

        $jsapi_ticket=$jsapi_ticket->ticket;
        $timestamp = time();
        $noncestr = "test";

        $url = $_POST['url'];
        echo "<p id='sign'>".sha1("jsapi_ticket=".$jsapi_ticket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url)."</p>";
        echo "<p id='timestamp'>".$timestamp."</p>";
        echo "<p id='noncestr'>".$noncestr."</p>";
        echo "<p id='url'>".$url."</p>";
?>