<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_interested());

function do_get_list_interested() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $result = mysqli_query($conn, "select * from stock where interest='y'");
    if (!$result) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
