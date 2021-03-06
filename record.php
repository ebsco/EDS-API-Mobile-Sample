<?php

include('app/app.php');
include('rest/EBSCOAPI.php');

$api = new EBSCOAPI();

$db = $_REQUEST['db'];
$an = $_REQUEST['an'];
$highlight = $_REQUEST['highlight'];
$highlight = str_replace(array(" ", "&", "-"), array(",", ",", ","), $highlight);
$result = $api->apiRetrieve($an, $db, $highlight);

$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : '';


if (isset($result['error'])) {
    $error = $result['error'];
} else {
    $error = null;
}


if ($debug == 'y' || $debug == 'n') {
    $_SESSION['debug'] = $debug;
}

$variables = array(
    'result' => $result,
    'error' => $error,
    'id' => 'record',
    'debug' => isset($_SESSION['debug']) ? $_SESSION['debug'] : ''
);

render('record.html', 'layout.html', $variables);

?>