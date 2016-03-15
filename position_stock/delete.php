<?php

namespace position_stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_delete());

function do_delete() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }
    $id = $_POST["id"];
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $ret = get_details_id($id, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $detail_ids = $ret["data"];

    // 删除details
    $stmt = "";
    foreach ($detail_ids as $d_id) {
        $stmt.="id=$d_id or ";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 4);
    $stmt = "delete from detail_stock where " . $stmt;
    $ret = mysqli_query($conn, $stmt);
    if (!$ret) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }

    // 删除仓位
    $ret = mysqli_query($conn, "delete from position_stock where id=$id");
    if (!$ret) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}

// 获得detail id
function get_details_id($pid, $conn) {
    $stmt = "select id from detail_stock "
            . "where id in (select detail_stock_id from position_detail_stock where position_stock_id = $pid)";
    $ret = mysqli_query($conn, $stmt);
    if (!$ret) {
        return array("status" => "error", "message" => mysqli_error($conn));
    }
    $ids = array();
    foreach ($ret as $row) {
        $ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $ids);
}
