<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = db_connect();
    $result = mysqli_query($conn, "select * from stock order by exchange,code") or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
//        $objects[] = mysqli_fetch_object($result);        // 注意 foreach跟这个是不能一起用的，这个只能跟for一起用
        // mysqli_fetch_object 是解析成对象class，mysqli_fetch_assoc解析成只带key的array，mysqli_fetch_all解析成只带数字偏移的array
        // mysqli_fetch_array 解析成既有偏移又有key的数组
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
