<?php
include('app/app.php');
include('rest/EBSCOAPI.php');

$api = new EBSCOAPI();


$searchTerm = str_replace('"', '', $_REQUEST['query']);
$fieldCode = $_REQUEST['fieldcode'] ? $_REQUEST['fieldcode'] : '';
$start = isset($_REQUEST['pagenumber']) ? $_REQUEST['pagenumber'] : 1;
$limit = isset($_REQUEST['resultsperpage']) ? $_REQUEST['resultsperpage'] : 5;
$sortBy = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'relevance';
$amount = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'detailed';
$mode = 'all';
$expander = isset($_REQUEST['expander']) ? $_REQUEST['expander'] : '';
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : '';
$Info = $api->getInfo();


if (isset($_REQUEST['back']) && isset($_SESSION['results'])) {

    $results = $_SESSION['results'];

} else if (isset($_REQUEST['option'])) {


    $results = $_SESSION['results'];
    $queryStringUrl = $results['queryString'];

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $actions = array();
    if (!empty($action)) {
        if (strstr($action, 'setsort(')) {
            $sortBy = str_replace(array('setsort(', ')'), array('', ''), $action);
            $start = 1;
        }
        if (strstr($action, 'setResultsperpage(')) {
            $limit = str_replace(array('setResultsperpage(', ')'), array('', ''), $action);
        }
        if (strstr($action, 'GoToPage(')) {
            $start = str_replace(array('GoToPage(', ')'), array('', ''), $action);
        }
        $actions['action'] = $action;
    }

    $view = isset($_REQUEST['view']) ? array('view' => $_REQUEST['view']) : array();
    $params = array_merge($actions, $view);
    $url = $queryStringUrl . '&' . http_build_query($params);
    $results = $api->apiSearch($url);

    $_SESSION['results'] = $results;

} else if (isset($_REQUEST['refine']) || isset($_GET['login'])) {

    if (isset($_REQUEST['action'])) {
        $actions = $_REQUEST['action'];
    } else {
        $actions = '';
    }
    $refineActions = array();
    if (is_array($actions)) {
        for ($i = 0; $i < count($actions); $i++) {
            $refineActions['action-' . ($i + 1)] = $actions[$i + 1];
        }
    } else {
        $refineActions['action'] = $actions;
    }
    $results = $_SESSION['results'];
    $queryStringUrl = $results['queryString'];

    $params = http_build_query($refineActions);

    $url = $queryStringUrl . '&' . $params;
    $results = $api->apiSearch($url);

    $_SESSION['results'] = $results;

    if (isset($_REQUEST['refine'])) $start = 1;

} else {

    $query = array();


    if (!empty($searchTerm)) {
        $term = urldecode($searchTerm);
        $term = str_replace('"', '', $term);
        $term = str_replace(',', "\,", $term);
        $term = str_replace(':', '\:', $term);
        $term = str_replace('(', '\(', $term);
        $term = str_replace(')', '\)', $term);

        if ($fieldCode != 'keyword') {
            $query_str = implode(":", array($fieldCode, $term));
        } else {
            $query_str = $term;
        }
        $query["query"] = $query_str;


    } else {
        $results = array();
    }


    $params = array(


        'sort' => $sortBy,


        'searchmode' => $mode,
        'relatedcontent' => 'rs',


        'view' => $amount,

        'includefacets' => 'y',
        'resultsperpage' => $limit,
        'pagenumber' => $start,

        'highlight' => 'y',
        'expander' => $expander
    );
    $params = array_merge($params, $query);
    $params = http_build_query($params);

    $results = $api->apiSearch($params);


    $_SESSION['results'] = $results;
}


if (isset($results['error'])) {
    $error = $results['error'];
    $results = array();
} else {
    $error = null;
}


if ($debug == 'y' || $debug == 'n') {
    $_SESSION['debug'] = $debug;
}


$variables = array(
    'searchTerm' => $searchTerm,
    'fieldCode' => $fieldCode,
    'results' => $results,
    'error' => $error,
    'start' => $start,
    'limit' => $limit,
    'refineSearchUrl' => '',
    'amount' => $amount,
    'sortBy' => $sortBy,
    'id' => 'results',
    'Info' => $Info,
    'debug' => isset($_SESSION['debug']) ? $_SESSION['debug'] : ''

);

render('results.html', 'layout.html', $variables);
?>