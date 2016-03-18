<?php

namespace position_stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_open());

function do_open() {
    if (!request_params_exist(array("json"))) {
        return array("status" => "error", "message" => "缺失字段json");
    }
    $param = json_decode($_POST["json"], true);
    $code = $param["code"];
    $lot = $param["lot"];
    $open_price = $param["open_price"];
    $open_date = $param["open_date"];
    $quit_price = $param["quit_price"];
    $account = $param["account"];
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    // 是否存在此持仓
    $ret = exist_position($code, $account, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $id = count($ret["data"]) == 0 ? null : $ret["data"][0];
    if ($id === null) {
        // 开仓
        $ret = mysqli_query($conn, "insert into position_stock(quit_price,action) values($quit_price,'卖出 <=')") or die_db_error($conn);
//        if (!$ret) {
//            $msg = mysqli_error($conn);
//            mysqli_close($conn);
//            return array("status" => "error", "message" => $msg);
//        }
        $id = mysqli_insert_id($conn);
    }

    // 交易记录写入detail
    $ret = insert_detail($code, $lot, $open_price, $open_date, $account, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    $detail_ids = $ret["data"];

    // 建立position_stock 和detail_stock联系
    $ret = make_position_detail_stock($id, $detail_ids, $conn);
    if ($ret["status"] !== "ok") {
        mysqli_close($conn);
        return $ret;
    }
    mysqli_close($conn);
    return array("data" => $id);
}

function exist_position($code, $account, $conn) {
    $stmt = "select id from v_position_stock where code='$code' and account='$account'";
    $ret = mysqli_query($conn, $stmt) or die_db_error($conn);
//    if (!$ret) {
//        return array("status" => "error", "message" => mysqli_error($conn));
//    }
    $ids = array();
    foreach ($ret as $row) {
        $ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $ids);
}

function insert_detail($code, $lot, $open_price, $open_date, $account, $conn) {
    $detail_ids = array();
    for ($i = 1; $i <= $lot; $i++) {
        $stmt = "insert into detail_stock (code,name,open_price,open_date,account) values("
                . "'$code',(select name from stock where code='$code'),$open_price,'$open_date','$account')";
        mysqli_query($conn, $stmt) or die_db_error($conn);
//        if (!$ret) {
//            return array("status" => "error", "message" => mysqli_error($conn));
//        }
        $detail_ids[] = mysqli_insert_id($conn);
    }
    return array("status" => "ok", "data" => $detail_ids);
}

function make_position_detail_stock($p_id, $d_ids, $conn) {
    foreach ($d_ids as $detail_id) {
        $stmt = "insert into position_detail_stock(position_stock_id, detail_stock_id) values($p_id,$detail_id)";
        mysqli_query($conn, $stmt) or die_db_error($conn);
//        if (!$ret) {
//            return array("status" => "error", "message" => mysqli_error($conn));
//        }
    }
}
