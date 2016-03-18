<?php

namespace trade_setting;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_save_default());

function do_save_default() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或json");
    }

    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $v = $param["v"];
    $stmt = "update setting set ";
    if (in_array("open_percent", $v)) {
        $open_percent = $param["open_percent"];
        $stmt.="open_percent=$open_percent,";
    }
    if (in_array("loss_percent", $v)) {
        $loss_percent = $param["loss_percent"];
        $stmt.="loss_percent=$loss_percent,";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 1) . " where id=$id";
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, $stmt) or die_db_error($conn);
//    if (!) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    mysqli_close($conn);
    return array("message" => "参数保存成功");
}
