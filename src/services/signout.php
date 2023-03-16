<?php 

session_start();
$_SESSION = array();
session_destroy();

//Access-Control-Allow-Origin セキュリティ面で設定
// 参考サイト: https://techblog.securesky-tech.com/entry/2021/12/09/
// 参考サイト: https://qiita.com/att55/items/2154a8aad8bf1409db2b
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
http_response_code(204);
