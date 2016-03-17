<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_close());

function do_close() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或者json");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $lot = $param["lot"];
    $price = $param["price"];
    $date = $param["date"];

    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $ret = get_closing_detail_ids($id, $lot, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $d_ids = $ret["data"];

    // 执行平仓
    if (count($d_ids) > 0) {
        $stmt = "";
        foreach ($d_ids as $detail_id) {
            $stmt.="id=$detail_id or ";
        }
        $stmt = substr($stmt, 0, strlen($stmt) - 4);
        $stmt = "update detail_futures set status='平',close_price=$price,close_date='$date' where " . $stmt;
        $ret = mysqli_query($conn, $stmt);
        if (!$ret) {
            $msg = mysqli_error($conn);
            mysqli_close($conn);
            return array("status" => "error", "message" => $msg);
        }
    }

    // 看还有多少未平仓的detail
    $ret = get_unclosed_detail_ids($id, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }

    $d_ids = $ret["data"];
    if (count($d_ids) == 0) {
        // 顺带删除仓位以及position_detail_id，sae上没有外键
        $ret = mysqli_query($conn, "delete from position_detail_futures where position_futures_id=$id");
        if (!$ret) {
            $msg = mysqli_error($conn);
            mysqli_close($conn);
            return array("status" => "error", "message" => $msg);
        }

        $ret = mysqli_query($conn, "delete from position_futures where id=$id");
        if (!$ret) {
            $msg = mysqli_error($conn);
            mysqli_close($conn);
            return array("status" => "error", "message" => $msg);
        }
    }
    mysqli_close($conn);
}

// 获得需要平仓的detail id
function get_closing_detail_ids($pid, $lot, $conn) {
    $stmt = "select id from detail_futures where id in (select detail_futures_id from position_detail_futures where position_futures_id=$pid) "
            . "and status='持' order by id limit $lot";
    $result = mysqli_query($conn, $stmt);
    if (!$result) {
        $msg = mysqli_error($conn);
        return array("status" => "error", "message" => $msg);
    }
    $d_ids = array();
    foreach ($result as $row) {
        $d_ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $d_ids);
}

// 获得未平仓的detail id
function get_unclosed_detail_ids($pid, $conn) {
    $stmt = "select id from detail_futures "
            . "where id in (select detail_futures_id from position_detail_futures where position_futures_id=$pid) "
            . "and status='持' "
            . "order by id";
    $result = mysqli_query($conn, $stmt);
    if (!$result) {
        return array("status" => "error", "message" => mysqli_error($conn));
    }
    $ids = array();
    foreach ($result as $row) {
        $ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $ids);
}
