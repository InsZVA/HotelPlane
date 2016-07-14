<?php
/**
 * Created by PhpStorm.
 * User: Welkin Ni
 * Date: 2016/7/12
 * Time: 18:45
 */
require_once "const.php";
class Weixin
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user,
            MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function sendMessage($postData)
    {
        $data=$postData->data;
        $user_id=$postData->userId;

        $access_token=$this->getToken();
        if(!$access_token)
            return false;

        $touser=$this->mysqli->query("select `open_id` from `user` where `user_id`=$user_id");

        if($touser=$touser->fetch_assoc())
            $touser=$touser['open_id'];

        $template_id=$data->template_id;
        $template_id=TEMPLATE_LIST[$template_id-1];
        $url=$data->url;
        $data=$data->content;
        $info=array(
            "touser"=>$touser,
            "template_id"=>$template_id,
            "url"=>$url,
            "data"=>$data
        );

        $info=json_encode($info);

        $ch=curl_init();

        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";
        curl_setopt($ch,CURLOPT_URL,$url.$access_token);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);

        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$info);
        $response=curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    private function getToken()
    {
        if(file_exists("token.txt"))
        {
            $token=fopen("token.txt","r");
            $access_token=fread($token,filesize("token.txt"));
            $access_token=json_decode($access_token);
            if(intval(time())-intval($access_token->time)>7000);
            return $access_token->access_token;
        }

        $ch=curl_init();

        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx81c1603b41b5f4f6&secret=15d9382d85bb018c56e3cc41bb299d5b";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);

        $access_token=curl_exec($ch);
        $access_token=json_decode($access_token);


        curl_close($ch);

        if(!isset($access_token->access_token))
            return false;

        $access_token->time=time();
        $token=fopen("token.txt","w");
        fwrite($token,json_encode($access_token));
        fclose($token);

        return $access_token->access_token;
    }
}