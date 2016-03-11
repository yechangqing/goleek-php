<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_delete());

function do_delete() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }
    $id = $_POST["id"];
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    // 取得还有哪些detail，并删除
    $result = mysqli_query($conn, "select id from detail_futures where id in (select detail_futures_id from position_detail_futures where position_futures_id=$id)");
    if (!$result) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    $detail_ids = array();
    foreach ($result as $row) {
        $detail_ids[] = $row["id"];
    }
    $stmt = "delete from detail_futures where ";
    foreach ($detail_ids as $unit) {
        $stmt.="id=$unit or ";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 4);
    if (!mysqli_query($conn, $stmt)) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }

    // 删除仓位
    if (!mysqli_query($conn, "delete from position_futures where id=$id")) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}
