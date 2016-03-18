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
    $link = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $id = $_POST["id"];
    $stmt = "delete from account where id=$id";
    mysqli_query($link, $stmt) or die_db_error($link);
//    if (!mysqli_query($link, $stmt)) {
//        $msg = mysqli_error($link);
//        mysqli_close($link);
//        return array("status" => "error", "message" => $msg);
//    }
    mysqli_close($link);
}
