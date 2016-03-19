<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_un_use());

function do_un_use() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }

    $id = $_POST["id"];
    $conn = db_connect();
    $ret = mysqli_query($conn, "update account set used='n' where id=$id") or die_db_error($conn);
    mysqli_close($conn);
}
