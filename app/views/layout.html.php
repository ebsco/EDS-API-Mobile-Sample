<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Search</title>
    <!-- Include meta tag to ensure proper rendering and touch zooming -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Include jQuery Mobile stylesheets -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">
    <link rel="stylesheet" href="web/custom.css">
    <!-- Include the jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


    <!-- Include the jQuery Mobile library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>

    <link rel="stylesheet" href="web/pubtype-icons.css"/>
    <link rel="shortcut icon" href="web/favicon.ico"/>
</head>

<body>
<div data-role="page">
    <div data-theme="b" data-role="header">
        <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') {
            echo '<h1>EDS</h1>';
        } else {
            echo '<div data-role="controlgroup" data-type="horizontal" class="ui-btn-left">
                    <a href="/" class="ui-btn ui-btn-icon-notext ui-icon-back" data-rel="back">Back</a>
                    <a class="ui-btn ui-btn-icon-notext ui-icon-home" id="home" href="./">Home</a></div>

                <h1>EDS</h1>';
        } ?>

        <?php if (!(isset($_SESSION['login']) || isset($login) || (validAuthIP("Config.xml") == true))) { ?>

            <?php if (isset($_REQUEST['db']) && isset($_REQUEST['an']) && isset($_REQUEST['resultId'])) {
                $params = array(
                    'path' => 'record',
                    'db' => $_REQUEST['db'],
                    'an' => $_REQUEST['an'],
                    'highlight' => $_REQUEST['highlight'],
                    'resultId' => $_REQUEST['resultId'],
                    'recordCount' => $_REQUEST['recordCount'],
                    'query' => $_REQUEST['query'],
                    'fieldcode' => $_REQUEST['fieldcode']
                );
                $params = http_build_query($params);
                ?>

                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?<?php echo $params; ?>">Login</a>
            <?php } else if (isset($refineSearchUrl)) {
                $params = array(
                    'path' => 'results',
                    'query' => $searchTerm,
                    'fieldcode' => $fieldCode
                );
                $params = http_build_query($params);
                ?>
                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?<?php echo $params; ?>">Login</a>
            <?php } else { ?>
                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?<?php echo $params; ?>">Login</a>
            <?php } ?>
        <?php } ?>
        <?php if (isset($_SESSION['login']) || isset($login)) { ?>
            <a class="ui-btn-right" data-icon="user" data-iconpos="right" data-ajax="false" href="logout.php">Logout</a>
        <?php } else { ?>
            <?php if (isset($_REQUEST['db']) && isset($_REQUEST['an']) && isset($_REQUEST['resultId'])) {
                $params = array(
                    'path' => 'record',
                    'db' => $_REQUEST['db'],
                    'an' => $_REQUEST['an'],
                    'highlight' => $_REQUEST['highlight'],
                    'resultId' => $_REQUEST['resultId'],
                    'recordCount' => $_REQUEST['recordCount'],
                    'query' => $_REQUEST['query'],
                    'fieldcode' => $_REQUEST['fieldcode']
                );
                $params = http_build_query($params);
                ?>
                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?<?php echo $params; ?>">Login</a>

            <?php } else if (isset($refineSearchUrl)) {
                $params = array(
                    'path' => 'results',
                    'query' => $searchTerm,
                    'fieldcode' => $fieldCode
                );
                $params = http_build_query($params);
                ?>
                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?<?php echo $params; ?>">Login</a>

            <?php } else { ?>
                <a class="ui-btn-right" data-icon="user" data-iconpos="right" href="login.php?path=index">Login</a>

            <?php } ?>

        <?php } ?>


        <?php if (basename($_SERVER['PHP_SELF']) == 'results.php') {
            echo '
            <div data-role="navbar">
                <ul>
                    <li>
                        <a id="refineb" href="#sideRefine">Refine search</a>
                    </li>
                    <li>
                        <a id="searchb" href="#sideSoptions">Search options</a>
                    </li>
                </ul>
            </div>
    </div>';
        } else if (basename($_SERVER['PHP_SELF']) == 'record.php') {
            echo '
         </div>';
        } else {
            echo '
            </div>
                <div data-role="header">
                    <h2 id="logo"><a href="index.php"><img src="web/vufind-logo.png"></a></h2>
                </div>';
        } ?>

        <div data-role="content" class="content">
            <?php echo $content; ?>
        </div>
        <?php
        $xml = "Config.xml";
        $dom = new DOMDocument();
        $dom->load($xml);
        $version = $dom->getElementsByTagName('Version')->item(0)->nodeValue;
        ?>
        <div data-role="footer" data-theme="b" class="footer">
            <h4>Need help?</h4>

            <div data-role="navbar">
                <ul>
                    <li><a href="http://vufinddemo.ebscohost.com/demo/Help/Home?topic=search" target="_blank">Search
                            Tips</a></li>
                    <li><a href="#">Ask a Librarian</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
            <h5><?php echo $version; ?></h5>
        </div>

    </div>
</body>

</html>

