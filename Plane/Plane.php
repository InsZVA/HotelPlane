<?php
/**
 * Created by PhpStorm.
 * User: Welkin Ni
 * Date: 2016/7/4
 * Time: 12:23
 */
class Plane
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user,
            MySQLConfig::$db_password, MySQLConfig::$db_name);
        $this->updatePlanes();
    }
    
    public function newPlane($data) {
        if(!isset($data->flight_number)) return false;
        if(!isset($data->start_city_id)) return false;
        if(!isset($data->start_airport)) return false;
        if(!isset($data->end_city_id)) return false;
        if(!isset($data->end_airport)) return false;
        if(!isset($data->start_time)) return false;
        if(!isset($data->end_time)) return false;
        if(!isset($data->remarks)) $data->remarks="";
        if(!isset($data->standard)) return false;
        if(!isset($data->type)) return false;
        if(!isset($data->price)) return false;

        $stmt = $this->mysqli->prepare("insert into plane(`flight_number`,`start_city_id`,
 `start_airport`, `end_city_id`,`end_airport`, `start_time`, `end_time`,`remarks`,
  `standard`, `type`, `price`) values(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sisissssisd', $data->flight_number, $data->start_city_id,
            $data->start_airport, $data->end_city_id, $data->end_airport, $data->start_time,
            $data->end_time, $data->remarks, $data->standard, $data->type, $data->price);
        $stmt->execute();
        $stmt->get_result();

        return $this->mysqli->insert_id;
    }

    private function updatePlanes()
    {
        $datetime=date('Y-m-d H:i:s');
        $this->mysqli->query("delete from `plane` where unix_timestamp(`start_time`)
 < unix_timestamp('$datetime')");
    }

    public function deletePlane($planeId) {
        $planeId = intval($planeId);
        return $this->mysqli->query("delete from `plane` where `plane_id` = $planeId");
    }

    public function editPlane($data)
    {
        if(!$data->plane_id) return false;
        if(!isset($data->flight_number)) return false;
        if(!isset($data->start_city_id)) return false;
        if(!isset($data->start_airport)) return false;
        if(!isset($data->end_city_id)) return false;
        if(!isset($data->end_airport)) return false;
        if(!isset($data->start_time)) return false;
        if(!isset($data->end_time)) return false;
        if(!isset($data->remarks)) $data->remarks="";
        if(!isset($data->standard)) return false;
        if(!isset($data->type)) return false;
        if(!isset($data->price)) return false;

        $sql="update `plane` set ";

        foreach($data as $k=>$v)
        {
            if($k=='plane_id')
                continue;
            if($sql=="update `plane` set ")
                $sql=$sql."`$k`='$v'";
            else
                $sql=$sql.",`$k`='$v'";
        }
        $plane_id=intval($data->plane_id);
        $sql=$sql." where `plane_id`=$plane_id";
        
        return $this->mysqli->query($sql);
    }

    public function listPlanes($offset, $num, $orderBy, $order, $standard) {
        if ($order != 'asc' && $order != 'desc') return false;
        $orderList=array("start_time","start_city_id");
        if(!in_array($orderBy,$orderList)) return false;
        $sql = "select * from `plane` where `standard`=? order by $orderBy $order limit ?,?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iii', $standard, $offset, $num);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->mysqli->query("select * from `plane` where `standard`='$standard'");
        if ($result) {
            $rows = [];
            $rows['amount']=mysqli_affected_rows($this->mysqli);
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }
    
    public function search($keyword,$offset,$num)
    {
        $sql="select * from `plane` where ";
        if(preg_match("[0-9]",$keyword))
        {
            if(preg_match("[a-zA-Z]",$keyword))
                $sql=$sql."`flight_number` ";
            else
                $sql=$sql."concat(`start_time`,`end_time`)";
        }
        else
        {
            $sql=$sql."concat(`start_airport`,`end_airport`)";
        }
        $count=$sql." like '%$keyword%'";
        $sql = $sql." like '%$keyword%' limit $offset,$num";

        $result = $this->mysqli->query($sql);
        $this->mysqli->query($count);
        if ($result) {
            $rows = [];
            $rows['amount']=mysqli_affected_rows($this->mysqli);
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

        return false;
    }

    public function findPlanes($data) {
        if(!isset($data->start_city_id)) return false;
        if(!isset($data->end_city_id)) return false;
        if(!isset($data->start_date)) return false;
        if(!isset($data->offset)) $data->offset=0;
        if(!isset($data->num)) $data->num=10;
        $time1=$data->start_date.' 00:00:00';
        $time2=$data->start_date.' 23:59:59';

        $sql = "select * from `plane` where `start_city_id`=? and `end_city_id`=?
and `start_time` between ? and ? limit ?,?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iissii', $data->start_city_id, $data->end_city_id,
            $time1, $time2, $data->offset, $data->num);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->mysqli->query("select * from `plane` where `start_city_id`=
$data->start_city_id and `end_city_id`=$data->end_city_id
and `start_time` between '$time1' and '$time2'");
        if ($result) {
            $rows = [];
            $rows['amount']=mysqli_affected_rows($this->mysqli);
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }
}