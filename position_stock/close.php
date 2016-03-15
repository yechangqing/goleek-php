<?php

namespace position_stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_close());

function do_close() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或json");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $lot = $param["lot"];
    $price = $param["price"];
    $date = $param["date"];

    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");

    // 选出需要平仓的detail的id
    $ret = get_closing_detail_ids($id, $lot, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $d_ids = $ret["data"];

    if (count($d_ids) > 0) {
        // 写入交易记录
        $ret = write_detail($price, $date, $d_ids, $conn);
        if ($ret["status"] !== "ok") {
            mysqli_close($conn);
            return $ret;
        }
    }

//    // 判断是否有余仓
    $ret = get_unclosed_detail_ids($id, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }

    $unclose_id = $ret["data"];
    if (count($unclose_id) == 0) {
        $ret = mysqli_query($conn, "delete from position_stock where id=$id");
        if (!$ret) {
            $msg = mysqli_error($conn);
            mysqli_close($conn);
            return array("status" => "error", "message" => $msg);
        }
    }
    mysqli_close($conn);
}

function get_closing_detail_ids($pid, $lot, $conn) {
    $stmt = "select id from detail_stock "
            . "where id in (select detail_stock_id from position_detail_stock where position_stock_id=$pid) "
            . "and status='持' "
            . "order by id "
            . "limit $lot";
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

function write_detail($close_price, $close_date, $d_ids, $conn) {
    $stmt = "";
    foreach ($d_ids as $detail_id) {
        $stmt.="id=$detail_id or ";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 4);
    $stmt = "update detail_stock set status='平', close_price=$close_price, close_date='$close_date' where " . $stmt;
    $ret = mysqli_query($conn, $stmt);
    if (!$ret) {
        return array("status" => "error", "message" => mysqli_error($conn));
    }
    return array("status" => "ok");
}

function get_unclosed_detail_ids($pid, $conn) {
    $stmt = "select id from detail_stock "
            . "where id in (select detail_stock_id from position_detail_stock where position_stock_id = $pid) "
            . "and status='持' order by id";
    $ret = mysqli_query($conn, $stmt);
    if (!$ret) {
        return array("status" => "error", mysqli_error($conn));
    }
    $d_ids = array();
    foreach ($ret as $row) {
        $d_ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $d_ids);
}
