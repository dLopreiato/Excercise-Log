<?php
require_once('../lib/server_variables.php');
require_once('../lib/force_tls.php');
require_once('../lib/api_output_handlers.php');
require_once('../lib/ApiSession.php');
require_once('../lib/http_headers.php');

try
{
    $session = new ApiSession(new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DBNAME));

    apiDataHandler($session->GetCurrentWorkoutCategory($_GET['date']));
}
catch (Exception $e)
{
    apiExceptionHandler($e);
}

?>