<?php

define("a328763fe27bba", true);
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/app_init.php";
mysql_prepared_execute("DELETE FROM auth_tokens WHERE expires_at < NOW()", []);
mysql_prepared_execute("DELETE FROM auth_otps WHERE expires_at < DATE_SUB(NOW(), INTERVAL 30 DAY)", []);

?>