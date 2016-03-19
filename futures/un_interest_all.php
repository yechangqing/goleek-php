<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_un_interest_all());

function do_un_interest_all() {
    $conn = db_connect();
    $stmt = "update futures set interest='n' where id>0";
    mysqli_query($conn, $stmt) or die_db_error($conn);
    mysqli_close($conn);
}
