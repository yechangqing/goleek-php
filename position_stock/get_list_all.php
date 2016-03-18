<?php

namespace position_stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    $result = mysqli_query($conn, "select * from v_position_stock order by id") or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["code"] = $row["code"];
        $tmp["name"] = $row["name"];
        $tmp["lot"] = $row["lot"];
        $tmp["open_price"] = $row["open_price"];
        $tmp["action"] = $row["action"];
        $tmp["quit_price"] = $row["quit_price"];
        $tmp["account"] = $row["account"];
        $tmp["open_date"] = $row["open_date"];

        $objects[] = $tmp;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
