<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_remove());

function do_remove() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }
    $id = $_POST["id"];
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    mysqli_query($conn, "delete from stock where id=$id") or die_db_error($conn);
    mysqli_close($conn);
}
