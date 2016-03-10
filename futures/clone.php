<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_clone());

function do_clone() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "参数id或json缺失");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $new_code = $param["newCode"];
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    if (!mysqli_query($conn, "update futures set code='$new_code' where id=$id")) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}
