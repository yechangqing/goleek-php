<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = db_connect();
    $stmt = "select * from futures order by exchange,code";
    $result = mysqli_query($conn, $stmt) or die_db_error($conn);
    $objects = array();
//    $number = mysqli_num_rows($result);
//    for ($i = 0; $i < $number; $i++) {
//        $row = mysqli_fetch_object($result);
//        array_push($objects, $row);
//    }
    foreach ($result as $row) {
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
