<?php
include('app/app.php');
require_once 'rest/EBSCOAPI.php';

$an = $_REQUEST['an'];
$db = $_REQUEST['db'];

$api = new EBSCOAPI();
$record = $api->apiRetrieve($an, $db);


if (empty($record['pdflink'])) {
    header("location: login.php?path=PDF&an=$an&db=$db");
} else {
    header("location: {$record['pdflink']}");
}


?>
