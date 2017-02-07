<?php
/**
 * Created by PhpStorm.
 * User: Welkin Ni
 * Date: 2016/7/21
 * Time: 23:09
 */
class Display
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user,
            MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function getDisplay()
    {
        $query=$this->mysqli->query("select * from `display`");
        $rows=[];
        while($row = $query->fetch_assoc()) {
            $rows[]=$row;
        }
        return json_encode($rows);
    }

    public function changeDisplay($data)
    {
        if(!isset($data->path)) return false;
        if(!isset($data->state)) return false;
        //if(!isset($data->weight)) return false;
        if(!isset($data->image_id)) return false;
        if(!isset($data->href)) return false;

        return $this->mysqli->query("update `display` set `path`='$data->path',`state`='$data->state',
        `href`='$data->href' where `image_id`='$data->image_id'");
    }

}