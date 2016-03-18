<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_open());

function do_open() {
    if (!request_params_exist(array("json"))) {
        return array("status" => "error", "message" => "缺失字段json");
    }
    $param = json_decode($_POST["json"], true);
    $contract = $param["contract"];
    $direct = $param["direct"];
    $lot = $param["lot"];
    $open_price = $param["open_price"];
    $open_date = $param["open_date"];
    $quit_price = $param["quit_price"];
    $account = $param["account"];
    $action = null;
    if ($lot <= 0) {
        return array("status" => "error", "message" => "交易手数需>0");
    }
    // 先看是否存在此持仓
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $ret = exist($contract, $direct, $account, $conn);
    if ($ret["status"] == "error") {
        return $ret;
    }
    $p_id = count($ret["data"]) == 0 ? null : $ret["data"][0];
    if ($p_id == null) {
        if ($direct === "多") {
            $action = "卖出平仓 <=";
        } else if ($direct === "空") {
            $action = "买入平仓 >=";
        } else {
            mysqli_close($conn);
            return array("status" => "error", "message" => "direct值错误");
        }

        // 写入position_futures
        mysqli_query($conn, "insert into position_futures (quit_price,action) values ($quit_price,'$action')") or die_db_error($conn);
//        $result = mysqli_query($conn, "insert into position_futures (quit_price,action) values ($quit_price,'$action')");
//        if (!$result) {
//            $msg = mysqli_error($conn);
//            mysqli_close($conn);
//            return array("status" => "error", "message" => $msg);
//        }
        $p_id = mysqli_insert_id($conn);
    }

    $ret = insert_detail($contract, $direct, $lot, $open_price, $open_date, $account, $conn);
    if ($ret["status"] == "error") {
        mysqli_close($conn);
        return $ret;
    }
    $d_ids = $ret["data"];    // detail id们
    // 建立position_detail_futures
    $ret = make_position_detail_futures($p_id, $d_ids, $conn);
    if ($ret["status"] == "error") {
        mysqli_close($conn);
        return $ret;
    }
    return array("data" => $p_id);
}

function exist($contract, $direct, $account, $conn) {
    $result = mysqli_query($conn, "select * from v_position_futures where contract='$contract' and direct='$direct' and account='$account'")
            or die_db_error($conn);
//    if (!$result) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    $ids = array();
    foreach ($result as $row) {
        $ids[] = $row["id"];
    }
    return array("status" => "ok", "data" => $ids);
}

function insert_detail($contract, $direct, $lot, $open_price, $open_date, $account, $conn) {
    $ids = array();
    for ($i = 1; $i <= $lot; $i++) {
        $stmt = "insert into detail_futures(contract,name,direct,open_price,open_date,margin,unit,account) "
                . "values('$contract',"
                . "(select name from futures where code='$contract'),'$direct',$open_price,'$open_date',"
                . "(select margin from futures where code='$contract'),(select unit from futures where code='$contract'),'$account')";
        $ret = mysqli_query($conn, $stmt) or die_db_error($conn);
//        if (!$ret) {
//            return array("status" => "error", "message" => mysqli_error($conn));
//        }
        $ids[] = mysqli_insert_id($conn);
    }
    return array("status" => "ok", "data" => $ids);
}

function make_position_detail_futures($p_id, $d_ids, $conn) {
    $stmt = "";
    foreach ($d_ids as $detail_id) {
        $stmt.="($p_id,$detail_id),";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 1);
    $stmt = "insert into position_detail_futures(position_futures_id,detail_futures_id) values " . $stmt;
    mysqli_query($conn, $stmt) or die_db_error($conn);
//    if (!$ret) {
//        return array("status" => "error", "message" => mysqli_error($conn));
//    }
    return array("status" => "ok");
}
