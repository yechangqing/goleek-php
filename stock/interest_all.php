<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_interest_all());

function do_interest_all() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, "update stock set interest='y' where id>0") or die_db_error($conn);
    mysqli_close($conn);
}
