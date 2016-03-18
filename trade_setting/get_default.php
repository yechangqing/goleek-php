<?php

namespace trade_setting;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_default());

function do_get_default() {
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    $result = mysqli_query($conn, "select * from setting order by id limit 1") or die_db_error($conn);

    // 只取第一条
    $object = mysqli_fetch_object($result);
    return array("data" => $object);
}
