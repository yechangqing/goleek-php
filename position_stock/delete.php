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
    // 先记录下相关的detail_futures的id和position_detail_futures的id
    $ret = get_relate_ids($id, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $detail_id = $ret["detail_stock"];
    $position_detail_id = $ret["position_detail_stock"];
    // 删除details
    $ret = delete_by_ids("detail_stock", $detail_id, $conn);
    if (!$ret) {
        mysqli_close($conn);
        return $ret;
    }
    // 删除position_detail
    $ret = delete_by_ids("position_detail_stock", $position_detail_id, $conn);
    if (!$ret) {
        mysqli_close($conn);
        return $ret;
    }

    if (!mysqli_query($conn, "delete from position_stock where id=$id")) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}

function get_relate_ids($pid, $conn) {
    // 返回两个array，一个是detail_stock，一个是position_detail_stock
    $stmt = "select * from position_detail_stock where position_stock_id=$pid";
    $ret = mysqli_query($conn, $stmt);
    if (!$ret) {
        return array("status" => "error", "message" => mysqli_error($conn));
    }
    $detail_stock = array();
    $position_detail_stock = array();
    foreach ($ret as $row) {
        $detail_stock[] = $row["detail_stock_id"];
        $position_detail_stock[] = $row["id"];
    }
    return array("status" => "ok", "detail_stock" => $detail_stock, "position_detail_stock" => $position_detail_stock);
}

function delete_by_ids($table, $ids, $conn) {
    $stmt = "";
    foreach ($ids as $d_id) {
        $stmt.="id=$d_id or ";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 4);
    $stmt = "delete from " . $table . " where " . $stmt;
    if (!mysqli_query($conn, $stmt)) {
        return array("status" => "error", "message" => $msg);
    }
    return array("status" => "ok");
}
