<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_interest());

function do_interest() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }
    $id = $_POST["id"];
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $stmt = "update futures set interest='y' where id=$id";
    if (!mysqli_query($conn, $stmt)) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}
