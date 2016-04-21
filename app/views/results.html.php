<?php
$queryStringUrl = $results['queryString'];

$refineParams = array(
    'refine' => 'y',
    'query' => $searchTerm,
    'fieldcode' => $fieldCode
);
$refineParams = http_build_query($refineParams);
$refineSearchUrl = "results.php?" . $refineParams;
$encodedSearchTerm = http_build_query(array('query' => $searchTerm));
$encodedHighLigtTerm = http_build_query(array('highlight' => $searchTerm));

?>

<div id="searchbox" class="topSearchBox" xmlns="http://www.w3.org/1999/html">
    <h4>Search options</h4>

    <form action="results.php">
        <input type="text" data-type="search" data-theme="a" name="query" id="lookfor"
               value="<?php echo $searchTerm ?>"/>
        <fieldset data-role="controlgroup">


            <input type="hidden" name="expander" value="fulltext"/>
            <?php
            $selected1 = '';
            $selected2 = '';
            $selected3 = '';
            if ($fieldCode == 'keyword') {
                $selected1 = "selected = 'selected'";
            }
            if ($fieldCode == 'AU') {
                $selected2 = "selected = 'selected'";
            }
            if ($fieldCode == 'TI') {
                $selected3 = "selected = 'selected'";
            } ?>
            <select name="fieldcode">
                <option id="type-keyword" name="fieldcode" value="keyword" <?php echo $selected1 ?> >Keyword
                </option>
                <?php if (!empty($Info['search'])) { ?>
                    <?php foreach ($Info['search'] as $searchField) {
                        if ($searchField['Label'] == 'Author') {
                            $fieldc = $searchField['Code']; ?>
                            <option id="type-author" name="fieldcode"
                                    value="<?php echo $fieldc; ?>"<?php echo $selected2; ?> >Author
                            </option>
                        <?php }
                        if ($searchField['Label'] == 'Title') {
                            $fieldc = $searchField['Code']; ?>
                            <option id="type-title" name="fieldcode"
                                    value="<?php echo $fieldc; ?>"<?php echo $selected3 ?> >Title
                            </option>
                        <?php }
                    } ?>
                <?php } ?>
            </select>
            <input id="submit" data-theme="a" type="submit" value="Search"/>
        </fieldset>


    </form>
</div>

<div id="searchoptions" class="topbar-resultList">
    <form action="pageOptions.php">
        <fieldset data-role="controlgroup">
            <label class="ui-hidden-accessible" for="sort"><b>Sort</b></label>
            <select name="sort" id="sort">
                <?php foreach ($Info['sort'] as $s) {
                    if ($sortBy == $s['Id']) { ?>
                        <option selected="selected"
                                value="<?php echo $s['Action']; ?>"><?php echo $s['Label'] ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $s['Action']; ?>"><?php echo $s['Label'] ?></option>
                    <?php }
                } ?>
            </select>

            <?php


            ?>

            <label class="ui-hidden-accessible" for="view"><b>Page options</b></label>
            <select name="view" id="view">
                <option id="detailed" value="detailed">Detailed
                </option>
                <option id="brief" value="brief">Brief</option>
                <option id="title" value="title">Title Only
                </option>
            </select>
            <?php $select = array(
                '5' => '',
                '10' => '',
                '20' => '',
                '30' => '',
                '40' => '',
                '50' => ''
            );
            if ($limit == 5) {
                $select['5'] = '  selected="selected"';
            }
            if ($limit == 10) {
                $select['10'] = '  selected="selected"';
            }
            if ($limit == 20) {
                $select['20'] = '  selected="selected"';
            }
            if ($limit == 30) {
                $select['30'] = '  selected="selected"';
            }
            if ($limit == 40) {
                $select['40'] = '  selected="selected"';
            }
            if ($limit == 50) {
                $select['50'] = '  selected="selected"';
            }
            ?>
            <!--<label class="ui-hidden-accessible" for="resultsp"><b>Results per page</b></label>
            <select name="resultsperpage" id="resultsp">
                <option <?php echo $select['5'] ?> value="setResultsperpage(5)">5
                </option>
                <option <?php echo $select['10'] ?> value="setResultsperpage(10)">10
                </option>
                <option <?php echo $select['20'] ?> value="setResultsperpage(20)">20
                </option>
                <option <?php echo $select['30'] ?> value="setResultsperpage(30)">30
                </option>
                <option <?php echo $select['40'] ?> value="setResultsperpage(40)">40
                </option>
                <option <?php echo $select['50'] ?> value="setResultsperpage(50)">50
                </option>
            </select>!-->

            <input type="hidden" value="<?php echo $searchTerm; ?>" name="query"/>
            <input type="hidden" value="<?php echo $fieldCode; ?>" name="fieldcode"/>
            <input data-theme="a" type="submit" value="Update">
        </fieldset>
    </form>
</div>

<div id="refine" class="ui-corner-all custom-corners">
    <div data-theme="b" data-inset="false" class="facets" data-role="collapsibleset">
        <div><h4>Refine Search</h4></div>

        <?php if (!empty($results['appliedFacets']) || !empty($results['appliedLimiters']) || !empty($results['appliedExpanders'])) { ?>

            <div data-theme="c" class="facet" data-collapsed="false" data-role="collapsible">
                <h3 class="facet-label">Remove Facets</h3>
                <ul data-role="listview" data-split-icon="delete" class="filters">
                    <!-- applied facets -->
                    <?php if (!empty($results['appliedFacets'])) { ?>
                        <?php foreach ($results['appliedFacets'] as $filter) { ?>
                            <?php foreach ($filter['facetValue'] as $facetValue) {
                                $action = http_build_query(array('action' => $facetValue['removeAction']));
                                ?>
                                <li data-icon="delete">
                                    <a href="<?php echo $refineSearchUrl . '&' . $queryStringUrl . '&' . $action; ?>"><?php echo $facetValue['Id']; ?>
                                        : <?php echo $facetValue['value']; ?></a>
                                </li>
                            <?php }
                        }
                    } ?>
                    <!-- Applied limiters -->
                    <?php if (!empty($results['appliedLimiters'])) { ?>
                        <?php foreach ($results['appliedLimiters'] as $filter) {
                            $limiterLabel = '';
                            foreach ($Info['limiters'] as $limiter) {
                                if ($limiter['Id'] == $filter['Id']) {
                                    $limiterLabel = $limiter['Label'];
                                    break;
                                }
                            }
                            $action = http_build_query(array('action' => $filter['removeAction']));
                            ?>
                            <li data-icon="delete">
                                <a href="<?php echo $refineSearchUrl . '&' . $queryStringUrl . '&' . $action; ?>">Limiter: <?php echo $limiterLabel; ?></a>
                            </li>
                        <?php }
                    } ?>
                    <!-- Applied expanders -->
                    <?php if (!empty($results['appliedExpanders'])) { ?>
                        <?php foreach ($results['appliedExpanders'] as $filter) {
                            $expanderLabel = '';
                            if (isset($Info['expanders'])) {
                                foreach ($Info['expanders'] as $exp) {
                                    if ($exp['Id'] == $filter['Id']) {
                                        $expanderLabel = $exp['Label'];
                                        break;
                                    }
                                }
                                $action = http_build_query(array('action' => $filter['removeAction']));
                            }
                            ?>
                            <li data-icon="delete">
                                <a href="<?php echo $refineSearchUrl . '&' . $queryStringUrl . '&' . $action; ?>">Expander: <?php echo $expanderLabel; ?></a>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (!empty($Info['limiters'])) { ?>
            <div data-theme="a" data-role="collapsible" class="limiters">
                <h3 class="facet-label">Limit your results</h3>

                <form action="limiter.php" method="get">
                    <fieldset data-theme="a" data-inset="false" data-role="controlgroup">
                        <?php for ($i = 0; $i < 3; $i++) { ?>
                            <?php $limiter = $Info['limiters'][$i]; ?>
                            <?php if ($limiter['Type'] == 'select') { ?>
                                <?php if (empty($results['appliedLimiters'])) { ?>
                                    <label>
                                        <input type="checkbox" value="<?php echo $limiter['Action']; ?>"
                                               name="<?php echo $limiter['Id']; ?>">
                                        <?php echo $limiter['Label'] ?>
                                    </label>

                                <?php } else {
                                    $flag = FALSE;
                                    foreach ($results['appliedLimiters'] as $filter) {
                                        if ($limiter['Id'] == $filter['Id']) {
                                            $flag = TRUE;
                                            break;
                                        }
                                    }
                                    if ($flag == TRUE) { ?>
                                        <label>
                                            <input type="checkbox" value="<?php echo $limiter['Action']; ?>"
                                                   name="<?php echo $limiter['Id']; ?>"
                                                   checked="checked"/>
                                            <?php echo $limiter['Label'] ?>
                                        </label>
                                    <?php } else { ?>
                                        <label>
                                            <input type="checkbox" value="<?php echo $limiter['Action']; ?>"
                                                   name="<?php echo $limiter['Id']; ?>"/>
                                            <?php echo $limiter['Label'] ?>
                                        </label>
                                    <?php }
                                }
                            }
                        } ?>

                        <input type="hidden" value="<?php echo $searchTerm; ?>" name="query"/>
                        <input type="hidden" value="<?php echo $fieldCode; ?>" name="fieldcode"/>
                        <input data-theme="a" type="submit" value="Update"/>
                    </fieldset>
                </form>

            </div>
        <?php } ?>
        <div data-theme="a" data-role="collapsible" class="expanders">
            <h3 class="facet-label">Expand your results</h3>

            <form action="expander.php">
                <fieldset data-role="controlgroup">


                    <?php foreach ($Info['expanders'] as $exp) {
                        if (empty($results['appliedExpanders'])) { ?>
                            <label>
                                <input type="checkbox" value="<?php echo $exp['Action']; ?>"
                                       name="<?php echo $exp['Id']; ?>"/>
                                <?php echo $exp['Label']; ?>
                            </label>

                        <?php } else {
                            $flag = FALSE;
                            foreach ($results['appliedExpanders'] as $aexp) {
                                if ($aexp['Id'] == $exp['Id']) {
                                    $flag = TRUE;
                                    break;
                                }
                            }

                            if ($flag == TRUE) { ?>

                                <label>
                                    <input type="checkbox" value="<?php echo $exp['Action']; ?>"
                                           name="<?php echo $exp['Id']; ?>" checked="checked"/>
                                    <?php echo $exp['Label']; ?>
                                </label>

                            <?php } else { ?>
                                <label>
                                    <input type="checkbox" value="<?php echo $exp['Action']; ?>"
                                           name="<?php echo $exp['Id']; ?>"/>
                                    <?php echo $exp['Label']; ?>
                                </label>
                            <?php }
                        }
                    } ?>
                    <input type="hidden" value="<?php echo $searchTerm; ?>" name="query"/>
                    <input type="hidden" value="<?php echo $fieldCode; ?>" name="fieldcode"/>
                    <input data-theme="a" type="submit" value="Update"/>
                </fieldset>


            </form>


        </div>
        <?php if (!empty($results['facets'])) {
        $i = 0; ?>

        <?php foreach ($results['facets'] as $facet) {
            $i++; ?>

            <?php if (!empty($facet['Label'])) { ?>

                <div class="facet" data-role="collapsible">
                    <h3 class="facet-label" id="flip<?php echo $i ?>"><?php echo $facet['Label']; ?></h3>
                    <ul data-filter="true" data-role="listview" class="facet-values"
                        id="panel<?php echo $i ?>">


                        <?php foreach ($facet['Values'] as $facetValue) {
                            $action = http_build_query(array('action' => $facetValue['Action']));
                            ?>
                            <li data-theme="a">

                                <a href="<?php echo $refineSearchUrl . '&' . $queryStringUrl . '&' . $action; ?>">
                                    <h6><?php echo $facetValue['Value']; ?></h6>

                                    <p>(<?php echo $facetValue['Count']; ?>)</p>
                                </a>

                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php } ?>
</div>

<?php if (empty($results['records'])) {
    echo '</div>';
} ?>

<div id="resultscontent">

    <div id="top c" class="ui-bar ui-bar-a ui-corner-all">
        <?php if ($debug == 'y') { ?>
            <div style="float:right"><a target="_blank" href="debug.php?result=y">Search response XML</a></div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <?php if (!empty($results)) { ?>
            <div class="statistics">
                <span id="rcount"><?php echo $results['recordCount']; ?></span> results
                for "<span><?php echo $searchTerm; ?></span>"
            </div>
        <?php } ?>
    </div>
    <div id="results c">
        <ul data-role="listview" data-divider-theme="b" data-inset="true" id="results" class="ui-listview-outer">
            <?php if (empty($results['records'])) { ?>
                <li class="result table-row">
                    <h2><i id="noresults">No results were found.</i></h2>
                </li>
            <?php } else { ?>
                <?php foreach ($results['records'] as $result) { ?>
                    <?php if (!empty($result['pubType'])) { ?>
                        <li id="pubtype" data-role="list-divider" data-theme="b"><?php echo $result['pubType'] ?></li>
                    <?php } ?>

                    <?php if ((!isset($_COOKIE['login'])) && $result['AccessLevel'] == 1) { ?>
                        <li>This record from <b>[<?php echo $result['DbLabel'] ?>]</b> cannot be
                            displayed to guests. Login for full access.
                        </li>
                    <?php } else { ?>

                        <?php if (!empty($result['RecordInfo']['BibEntity']['Titles'])) { ?>
                            <li class="title">
                            <?php foreach ($result['RecordInfo']['BibEntity']['Titles'] as $Ti) { ?>
                                <a href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId']; ?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm; ?>&fieldcode=<?php echo $fieldCode; ?>"><?php echo $Ti['TitleFull']; ?></a>
                                </li>
                            <?php }
                        } else { ?>
                            <li class="title">
                                <a href="record.php?db=<?php echo $result['DbId']; ?>&an=<?php echo $result['An']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId']; ?>&recordCount=<?php echo $results['recordCount']; ?>&<?php echo $encodedSearchTerm; ?>&fieldcode=<?php echo $fieldCode; ?>"><?php echo "Title is not Aavailable"; ?></a>
                            </li>
                        <?php } ?>

                        <?php if (!empty($result['Items']['TiAtl'])) { ?>
                            <li>
                                <?php foreach ($result['Items']['TiAtl'] as $TiAtl) {
                                    echo $TiAtl['Data'];
                                } ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <li class="authors">
                        <?php if (isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Titles'])) { ?>

                            <span style="font-style: italic; ">
                                                <?php foreach ($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['Titles'] as $title) { ?>
                                                    <?php echo $title['TitleFull'] ?>,
                                                <?php } ?>
                                            </span>

                        <?php } ?>

                        <?php if (isset($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'])) { ?>
                            <?php foreach ($result['RecordInfo']['BibRelationships']['IsPartOfRelationships']['date'] as $date) { ?>
                                Published: <?php echo $date['M'] ?>/<?php echo $date['D'] ?>/<?php echo $date['Y'] ?>
                            <?php } ?>
                        <?php } ?>
                    </li>
                    <?php if (!empty($result['Items']['Au'])) { ?>
                        <li data-iconpos="right" data-inset="false" data-role="collapsible" class="authors">
                            <h3>Authors</h3>
                            <ul data-role="listview" id="bList">
                                <?php foreach ($result['Items']['Au'] as $Author) { ?>
                                    <?php echo $Author['Data']; ?>;
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if (isset($result['Items']['Ab'])) { ?>

                        <li data-iconpos="right" data-inset="false" data-role="collapsible"
                            id="abstract<?php echo $result['ResultId']; ?>" class="abstract">
                            <h3>Abstract</h3>
                            <?php foreach ($result['Items']['Ab'] as $Abstract) { ?>
                                <?php
                                $xml = "Config.xml";
                                $dom = new DOMDocument();
                                $dom->load($xml);
                                $length = $dom->getElementsByTagName('AbstractLength')->item(0)->nodeValue;
                                if ($length == 'Full') {
                                    echo $Abstract['Data'];
                                } else {
                                    $data = str_replace(array('<span class="highlight">', '</span>'), array('', ''), $Abstract['Data']);
                                    $data = substr($data, 0, $length) . '...';

                                    echo $data;
                                }
                                ?>
                            <?php } ?>
                        </li>

                    <?php } ?>
                    <?php if (!empty($result['Items']['Su'])) { ?>
                        <li data-iconpos="right" data-inset="false" data-role="collapsible" class="subjects">
                            <h3>Subjects</h3>
                            <ul data-role="listview" id="bList">


                                <?php foreach ($result['Items']['Su'] as $Subject) { ?>
                                    <?php echo $Subject['Data']; ?>;
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <li class="links">
                        <?php if ($result['HTML'] == 1) { ?>
                        <?php if ((!isset($_COOKIE['login'])) && $result['AccessLevel'] == 2) { ?>
                            <a target="_blank" class="icon html fulltext"
                               href="login.php?path=HTML&an=<?php echo $result['An']; ?>&db=<?php echo $result['DbId']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId']; ?>&recordCount=<?php echo $results['recordCount'] ?>&<?php echo $encodedSearchTerm; ?>&fieldcode=<?php echo $fieldCode; ?>"><span>Full
                                                Text</span></a>
                        <?php } else { ?>
                        <span> <a target="_blank" class="icon html fulltext"
                                  href="record.php?an=<?php echo $result['An']; ?>&db=<?php echo $result['DbId']; ?>&<?php echo $encodedHighLigtTerm ?>&resultId=<?php echo $result['ResultId']; ?>&recordCount=<?php echo $results['recordCount'] ?>&<?php echo $encodedSearchTerm; ?>&fieldcode=<?php echo $fieldCode; ?>#html"><span>Full
                                                Text</span></a>
                            <?php } ?>
                            <?php } ?>
                            <?php if (!empty($result['PDF'])) { ?>
                                <a target="_blank" class="icon pdf fulltext"
                                   href="PDF.php?an=<?php echo $result['An'] ?>&db=<?php echo $result['DbId'] ?>"><span>Full
                                            Text</span></a>
                            <?php } ?>
                    </li>
                    <?php if (!empty($result['CustomLinks'])) { ?>
                        <li class="links">
                            <?php if (count($result['CustomLinks']) <= 3) { ?>

                                <?php foreach ($result['CustomLinks'] as $customLink) { ?>

                                    <a href="<?php echo $customLink['Url']; ?>" class="icon_custom"
                                       title="<?php echo $customLink['MouseOverText']; ?>"><?php echo $customLink['Name']; ?>
                                    </a>

                                <?php } ?>

                            <?php } else { ?>

                                <?php for ($i = 0; $i < 3; $i++) {
                                    $customLink = $result['CustomLinks'][$i];
                                    ?>

                                    <a href="<?php echo $customLink['Url']; ?>"
                                       title="<?php echo $customLink['MouseOverText']; ?>"><?php echo $customLink['Name']; ?></a>

                                <?php } ?>

                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if (!empty($result['FullTextCustomLinks'])) { ?>

                        <?php if (count($result['FullTextCustomLinks']) <= 3) { ?>
                            <?php foreach ($result['FullTextCustomLinks'] as $customLink) { ?>
                                <li class="links">
                                    <a href="<?php echo $customLink['Url']; ?>"
                                       title="<?php echo $customLink['MouseOverText']; ?>"><?php echo $customLink['Name']; ?>
                                    </a>
                                </li>

                            <?php } ?>
                        <?php } else { ?>
                            <?php for ($i = 0; $i < 3; $i++) {
                                $customLink = $result['FullTextCustomLinks'][$i];
                                ?>
                                <li class="links">
                                    <a href="<?php echo $customLink['Url']; ?>"
                                       title="<?php echo $customLink['MouseOverText']; ?>"><?php echo $customLink['Name']; ?></a>
                                </li>
                            <?php } ?>

                        <?php } ?>

                    <?php } ?>


                <?php } ?>
            <?php } ?>
        </ul>
        <div style="display: none" id="divload">
            <button id="next" class="ui-btn ui-corner-all ui-btn-b">Load more</button>
        </div>

    </div>

</div>


<script>


    var sideRefine = '<div data-role="panel" id="sideRefine" data-position="left" data-display="overlay" data-theme="b">' + $('#refine').remove().html() + '</div>';
    var sideSoptions = '<div data-role="panel" id="sideSoptions" data-position="right" data-display="overlay" data-theme="b">' + $('#searchbox').remove().html() + $('#searchoptions').remove().html() + '</div>';


    $(document).one('pagecontainershow', function () {

        var activePage = $.mobile.pageContainer.pagecontainer("getActivePage");
        activePage.prepend(sideRefine);
        activePage.prepend(sideSoptions);

        $("[data-role=panel]").panel().enhanceWithin();

        $("#sideRefine a").click(function (e) {
            e.preventDefault();
            $.mobile.pageContainer.pagecontainer('change', $(this).attr("href"), {
                changeHash: false
            });
        });

        $("#lookfor").keyup(function (event) {
            if ($.trim($(this).val()).length > 0) {
                $("#submit").button("enable");
            } else {
                $("#submit").button("disable");
            }
        });


        $('ul#results li').filter(function () {
            var text = $(this).text().replace(/\s*/g, '');
            return !text;
        }).hide();


        $("#bList > a").each(function () {
            $(this).next('sup').remove();
            $(this).wrap("<li>");
        });

        $("[id=bList]").each(function () {
            $(this).html($(this).html().replace(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi, '<li><email>$1</email></li>'));
        });

        $("[id=bList]").contents().filter(function () {
            return this.nodeType === 3;
        }).remove();

        $("[id=bList]").listview().listview("refresh");


        var count = $('#rcount').text();

        var pagenum = 1;

        var next = $("#divload");


        if (count < 6) {
            next.hide();
        } else {
            next.show();
        }


        $("select#view")
            .find("#<?php echo $amount; ?>").prop("selected", true);

        $("select#view").selectmenu().selectmenu("refresh", true);


        next.click(function (event) {

            $.mobile.loading('show');
            pagenum++;
            var data = $.get("pageOptions.php", {
                query: "<?php echo $searchTerm; ?>",
                fieldcode: "<?php echo $fieldCode;?>",
                pagenumber: "GoToPage(" + pagenum + ")"
            });


            data.done(function (data) {
                $.mobile.loading('hide');

                if (pagenum == Math.ceil(count / 5) || $(data).find("#noresults").length) {
                    next.hide();
                } else {
                    next.show();
                }

                var res = $(data).find("ul#results li");

                $(res).filter(function () {
                    var text = $(this).text().replace(/\s*/g, '');
                    return !text;
                }).hide();


                $(res).find("#bList > a").each(function () {
                    $(this).next('sup').remove();
                    $(this).wrap("<li>");
                });

                $(res).find("[id=bList]").each(function () {
                    $(this).html($(this).html().replace(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi, '<li><email>$1</email></li>'));
                });

                $(res).find("[id=bList]").contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();

                $("ul#results").append(res)
                    .listview("refresh");

                $("ul#results [data-role=collapsible]").collapsible().collapsible("refresh");
                $("ul#results [data-role=listview]").listview().listview("refresh");


            })


        });

    });


</script>