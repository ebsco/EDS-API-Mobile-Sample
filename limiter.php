<?php
session_start();
include "rest/EBSCOAPI.php";

$api = new EBSCOAPI();
$Info = $api->getInfo();
$results = $_SESSION['results'];
$queryStringUrl = $results['queryString'];

$addLimiterActions = array();
$removeLimiterActions = array();


$i = 1;
foreach ($Info['limiters'] as $limiter) {
    if (isset($_REQUEST[$limiter['Id']])) {
        $addLimiterActions['action[' . $i . ']'] = str_replace('value', 'y', $limiter['Action']);
        $i++;
    } else {
        foreach ($results['appliedLimiters'] as $filter) {
            if ($filter['Id'] == $limiter['Id']) {
                $removeLimiterActions['action[' . $i . ']'] = str_replace('value', 'y', $filter['removeAction']);
                $i++;
            }
        }
    }
}

$searchTerm = $_REQUEST['query'];
$fieldCode = $_REQUEST['fieldcode'];
$params = array(
    'refine' => 'y',
    'query' => $searchTerm,
    'fieldcode' => $fieldCode,
);
$params = array_merge($params, $addLimiterActions);
$params = array_merge($params, $removeLimiterActions);
$url = 'results.php?' . http_build_query($params) . '&' . $queryStringUrl;

header("location: {$url}");
?>
