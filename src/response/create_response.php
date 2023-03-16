<?php

function create_response($status, $message)
{
  //JSON_UNESCAPED_UNICODE: マルチバイト Unicode 文字をそのままの形式で扱う
  //JSON_PRETTY_PRINT: 返される結果の書式を、スペースを使って整える
  //参考サイト: https://www.php.net/manual/ja/json.constants.php
  $json = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=utf-8");
  //HTTP レスポンスのステータスコードを取得したり設定したりする
  http_response_code($status);
  echo $json;
}
