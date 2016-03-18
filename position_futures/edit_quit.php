<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_edit_quit());

function do_edit_quit() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或json");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $action = $param["action"];
    $price = $param["price"];
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, "update position_futures set action='$action', quit_price=$price where id=$id") or die_db_error($conn);
    mysqli_close($conn);
}
