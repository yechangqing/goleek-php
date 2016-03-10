<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_futures_used_data());

function do_get_list_futures_used_data() {
    $connect = @mysqli_connect(HOST, USER, PASSWD, DB) or die("连接数据库失败");

    $stmt = "select * from account where type='期货' and used='y'";
    $result = mysqli_query($connect, $stmt);
    if (!$result) {
        $msg = mysqli_error($connect);
        mysqli_close($connect);
        return array("status" => "error", "message" => $msg);
    }
    $number = mysqli_num_rows($result);
    $objects = array();
    for ($i = 0; $i < $number; $i++) {
        $objects[$i] = mysqli_fetch_object($result);
    }
    mysqli_close($connect);
    return array("status" => "ok", "data" => $objects);
}