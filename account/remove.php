<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_remove());

function do_remove() {
    if (!request_params_exist(array("id"))) {
        global $status;
        $status = "error";
        $message = "error";
        return array("status" => "error", "message" => "缺失字段id");
    }
    $link = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    $id = $_POST["id"];
    $stmt = "delete from account where id=$id";
    mysqli_query($link, $stmt) or die_db_error($link);
    mysqli_close($link);
}
