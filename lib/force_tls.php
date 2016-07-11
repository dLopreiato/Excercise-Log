<?php
require_once('server_variables.php');

if (FORCE_TLS && (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    exit();
}
?>