<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $result = mysqli_query($conn, "select * from v_position_futures");
    if (!$result) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
        // 不返回is_ready_close了，客户端也相应取消这个字段
    }
    return array("data" => $objects);
}
