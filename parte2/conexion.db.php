<?php 
//error_reporting(0);
$link = mysqli_connect("127.0.0.1", "root", "", "tutorial_tradebot");
if (!$link) {
    echo "DB Error connection";
    exit;
}
mysqli_set_charset($link, "utf8");
?>