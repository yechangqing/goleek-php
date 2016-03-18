<?php

namespace trade_setting;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_default());

function do_get_default() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $result = mysqli_query($conn, "select * from setting order by id limit 1") or die_db_error($conn);
//    if (!$result) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }

    // 只取第一条
    $object = mysqli_fetch_object($result);
    return array("data" => $object);
}
