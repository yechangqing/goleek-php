<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_interested());

function do_get_list_interested() {
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    $result = mysqli_query($conn, "select * from stock where interest='y' order by exchange,code") or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
