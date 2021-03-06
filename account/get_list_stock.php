<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_stock());

function do_get_list_stock() {
    $conn = db_connect();
    $stmt = "select * from account where type='股票' order by id";
    $result = mysqli_query($conn, $stmt) or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        // 字段刚好一样，因此不用转换了
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
