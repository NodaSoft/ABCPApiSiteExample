<?php
require_once '__autoload.php';

$response = new AjaxRouter($_REQUEST);
echo $response->run();