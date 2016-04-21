<?php
$results = $results = $_SESSION['results'];
$queryStringUrl = $results['queryString'];
$encodedQuery = http_build_query(array('query' => $_REQUEST['query']));
$encodedHighLigtTerm = http_build_query(array('highlight' => $_REQUEST['highlight']));
?>
<div id="recordcontent">
    <div class="topbar">
        <div data-role="navbar">
            <ul id="navi">
                <?php if ($_REQUEST['resultId'] > 1) { ?>
                    <li>
                        <a id="prevButton" class="ui-btn navi"
                           href="recordSwich.php?<?php echo $encodedQuery; ?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>&resultId=<?php echo($_REQUEST['resultId'] - 1) ?>&<?php echo $queryStringUrl; ?>">Previous</a>
                    </li>
                <?php }

                if ($_REQUEST['resultId'] < $_REQUEST['recordCount']) { ?>
                    <li>
                        <a id="nextButton" class="ui-btn navi"
                           href="recordSwich.php?<?php echo $encodedQuery; ?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>&resultId=<?php echo($_REQUEST['resultId'] + 1) ?>&<?php echo $queryStringUrl; ?>">Next</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div style="display: none;" class="ui-bar ui-bar-b" id="hint">
            <p>You can also swipe left/right to navigate.</p>
        </div>
        <?php if ($debug == 'y') { ?>
            <div><a target="_blank" href="debug.php?record=y">Retrieve response XML</a></div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php } ?>
    </div>
    <div id="top">
        <?php if ((!isset($_COOKIE['login'])) && $result['AccessLevel'] == 1){ ?>
            <p>This record from <b>[<?php echo $result['DbLabel']; ?>]</b> cannot be displayed to guests.<br><a
                    href="login.php?path=record&db=<?php echo $_REQUEST['db'] ?>&an=<?php echo $_REQUEST['an'] ?>&<?php echo $encodedHighLigtTerm; ?>&resultId=<?php echo $_REQUEST['resultId'] ?>&recordCount=<?php echo $_REQUEST['recordCount'] ?>&<?php echo $encodedQuery; ?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>">Login</a>
                for full access.</p>
        <?php }else{ ?>
        <h3 class="ui-bar ui-bar-a ui-corner-all">
            <?php if (!empty($result['Items'])) {
                echo $result['Items'][0]['Data'];
            } ?>
        </h3>
    </div>
    <div class="ui-corner-all">

        <div class="table-cell span-15">

            <?php if (!empty($result['Items'])) { ?>
                <?php for ($i = 1; $i < count($result['Items']); $i++) { ?>
                    <div class="ui-bar ui-bar-b">
                        <h3><?php echo $result['Items'][$i]['Label']; ?></h3>
                    </div>
                    <div style="text-overflow: ellipsis" class="ui-body ui-body-a">


                        <?php if ($result['Items'][$i]['Label'] == 'URL') { ?>
                            <span id="url"><?php echo $result['Items'][$i]['Data'] ?></span>
                        <?php } else { ?>
                            <?php echo $result['Items'][$i]['Data']; ?>

                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (!empty($result['pubType'])) { ?>
                <div class="ui-bar ui-bar-b">
                    <h4>PubType</h4>
                </div>

                <div class="ui-body ui-body-a">
                    <?php echo $result['pubType'] ?>
                </div>

            <?php } ?>
            <?php if (!empty($result['DbLabel'])) { ?>
                <div class="ui-bar ui-bar-b">
                    <h4>Database</h4>
                </div>

                <div class="ui-body ui-body-a">
                    <?php echo $result['DbLabel']; ?>
                </div>

            <?php } ?>
            <?php if ((!isset($_COOKIE['login'])) && $result['AccessLevel'] == 2) { ?>

                <div class="ui-bar ui-bar-a">This record cannot be displayed to
                    guests. Login for full access.
                </div>

            <?php } ?>

            <?php if (!empty($result['htmllink'])) { ?>
                <div id="html">
                    <?php echo $result['htmllink'] ?>
                </div>
            <?php } ?>

            <div class="jacket">
                <?php if (!empty($result['ImageInfo'])) { ?>
                    <img width="150px" height="200px" src="<?php echo $result['ImageInfo']['medium']; ?>"/>
                <?php } ?>
            </div>
            <div data-role="collapsible" data-theme="b" class="table-cell floatleft">
                <h3>Links</h3>
                <?php if (!empty($result['PLink'])){ ?>
                <ul data-role="listview" class="table-cell-box">
                    <li>
                        <a href="<?php echo $result['PLink'] ?>">
                            View in EDS
                        </a>
                    </li>

                    <?php } ?>

                    <?php if (!empty($result['PDF']) || $result['HTML'] == 1){ ?>
                    <li>


                        <?php if (!empty($result['PDF'])){ ?>

                        <a target="_blank" class="icon pdf fulltext"
                           href="PDF.php?an=<?php echo $result['An'] ?>&db=<?php echo $result['DbId'] ?>">
                            PDF full text
                        </a>
                    </li>
                <?php } ?>
                    <?php if ($result['HTML'] == 1) { ?>
                        <?php if ((!isset($_COOKIE['login'])) && $result['AccessLevel'] == 2) { ?>
                            <li>
                                <a target="_blank" class="icon html fulltext"
                                   href="login.php?path=HTML&an=<?php echo $_REQUEST['an']; ?>&db=<?php echo $_REQUEST['db']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $_REQUEST['resultId']; ?>&recordCount=<?php echo $_REQUEST['recordCount'] ?>&<?php echo $encodedQuery; ?>&fieldcode=<?php echo $_REQUEST['fieldcode']; ?>">
                                    HTML full text
                                </a>
                            </li>
                        <?php } else { ?>
                            <li>
                                <a class="icon html fulltext" href="#html">HTML Full Text</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            <?php } ?>

                <?php if (!empty($result['CustomLinks'])) { ?>
                    <ul data-role="listview" class="table-cell-box">
                        <label>Custom Links:</label>
                        <?php foreach ($result['CustomLinks'] as $customLink) { ?>
                            <li>
                                <a href="<?php echo $customLink['Url']; ?>"
                                   title="<?php echo $customLink['MouseOverText']; ?>"><img
                                        src="<?php echo $customLink['Icon'] ?>"/> <?php echo $customLink['Text']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <?php if (!empty($result['FullTextCustomLinks'])) { ?>
                    <ul data-role="listview" class="table-cell-box">
                        <label>Custom Links:</label>
                        <?php foreach ($result['FullTextCustomLinks'] as $customLink) { ?>
                            <li>
                                <a href="<?php echo $customLink['Url']; ?>"
                                   title="<?php echo $customLink['MouseOverText']; ?>"><img
                                        src="<?php echo $customLink['Icon'] ?>"/> <?php echo $customLink['Text']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>

    $(document).one('pagecontainershow', function (event, ui) {
        $("#url searchlink").replaceWith("<a href='" + $("#url searchlink").html() + "'>" + $("#url searchlink").html() + "</a>");

        $("#nextButton").click(function (e) {
            e.preventDefault();
            $.mobile.pageContainer.pagecontainer('change', $(this).attr("href"), {
                transition: 'slidefade',
                changeHash: false

            });
        });

        $("#prevButton").click(function (e) {
            e.preventDefault();
            $.mobile.pageContainer.pagecontainer('change', $(this).attr("href"), {
                transition: 'slidefade',
                reverse: true,
                changeHash: false

            });
        });

        $("#recordcontent")
            .on("swipeleft", function () {
                $("#nextButton").click();
            })
            .on("swiperight", function () {
                $("#prevButton").click();
            });

        if ($("#prevButton").length == 0) {

            $("#hint").slideDown(300).delay(5000).slideUp(300);
        }

    });

</script>
