<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_stock_used());

function do_get_list_stock_used() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $stmt = "select * from account where type='股票' and used='y'";
    $result = mysqli_query($conn, $stmt);
    if (!$result) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    $objects = array();
    foreach ($result as $row) {
        // 字段刚好一样，因此不用转换了
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
