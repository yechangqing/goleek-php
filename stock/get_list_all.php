<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $result = mysqli_query($conn, "select * from stock");
    if (!$result) {
        $msg = mysqli_errno($conn) . ": " . mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
//        $objects[] = mysqli_fetch_object($result);        // 注意 foreach跟这个是不能一起用的，这个只能跟for一起用
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
