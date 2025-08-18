<?php
if(!defined("a328763fe27bba")){ define("a328763fe27bba", true); }
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../app_init.php";
header("Content-Type: application/javascript; charset=utf-8");
if (empty($_COOKIE['pre_auth_js']) || $_COOKIE['pre_auth_js'] !== '1') {
  http_response_code(403);
  echo "// forbidden";
  exit;
}
// serve the real JS
readfile(__DIR__ . "/main.js");
