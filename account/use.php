<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_use());

function do_use() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }

    $id = $_POST["id"];
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $ret = mysqli_query($conn, "update account set used='y' where id=$id") or die_db_error($conn);
//    if (!$ret) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    mysqli_close($conn);
}
