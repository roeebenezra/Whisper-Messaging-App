<?php
define("a328763fe27bba", true);
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/app_init.php";
header("Content-Type: application/json; charset=utf-8");
$token = $_POST['token'] ?? null;
if(!$token || !preg_match('/^[A-Fa-f0-9]{64}$/', $token)) { echo json_encode(['ok'=>false,'error'=>'missing/invalid token']); exit; }
mysql_prepared_execute("DELETE FROM auth_tokens WHERE token=?", [$token]);
echo json_encode(['ok'=>true]);
