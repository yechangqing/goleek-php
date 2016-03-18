<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_futures_data());

function do_get_list_futures_data() {
    //连接数据库
    $link1 = @mysqli_connect(HOST, USER, PASSWD) or die_db_link();

// 选择表，可以和上一步连在一起
    mysqli_select_db($link1, DB) or die("<h2>表不存在<h2>");

// 查询所有用户
    $stmt = "select * from account where type='期货' order by id";
    $result = mysqli_query($link1, $stmt);
    if ($result) {
        $number = mysqli_num_rows($result);
        $objects = array();
        for ($i = 0; $i < $number; $i++) {
//            $line = mysqli_fetch_array($result, MYSQL_ASSOC);
            $objects[$i] = mysqli_fetch_object($result);
        }
        // 关闭数据库
        mysqli_close($link1);
        return array("data" => $objects);
    } else {
        // 关闭数据库
        $msg = mysqli_errno($link1);
        mysqli_close($link1);
        echo array("status" => "error", "message" => $msg);
    }
}
