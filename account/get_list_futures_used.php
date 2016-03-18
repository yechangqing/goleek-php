<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_futures_used_data());

function do_get_list_futures_used_data() {
    $connect = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();

    $stmt = "select * from account where type='期货' and used='y' order by id";
    $result = mysqli_query($connect, $stmt) or die_db_error($connect);
    $number = mysqli_num_rows($result);
    $objects = array();
    for ($i = 0; $i < $number; $i++) {
        $objects[$i] = mysqli_fetch_object($result);
    }
    mysqli_close($connect);
    return array("status" => "ok", "data" => $objects);
}
