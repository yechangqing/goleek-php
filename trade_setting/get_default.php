<?php

namespace trade_setting;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_default());

function do_get_default() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $result = mysqli_query($conn, "select * from setting order by id limit 1");
    if (!$result) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }

    // 只取第一条
    $object = mysqli_fetch_object($result);
    return array("data" => $object);
}
