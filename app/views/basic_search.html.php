<?php
$api = new EBSCOAPI();
$Info = $api->getInfo();
?>
<div id="toptabcontent">
    <div class="searchHomeContent">
        <div class="searchHomeForm">
            <div class="searchform">
                <form data-transition="slidefade" action="results.php">
                    <div>
                        <input data-type="search" placeholder="Type here..." type="text" name="query"
                               id="lookfor"/>
                        <input type="hidden" name="expander" value="fulltext"/>
                        <input id="submit" disabled type="submit" data-theme="b" value="Search"/>
                    </div>

                    <fieldset data-role="controlgroup">
                        <input type="radio" id="type-keyword" name="fieldcode" value="keyword" checked="checked"/>
                        <label for="type-keyword">Keyword</label>

                        <?php if (!empty($Info['search'])) { ?>
                            <?php foreach ($Info['search'] as $searchField) {
                                if ($searchField['Label'] == 'Author') {
                                    $fieldCode = $searchField['Code']; ?>


                                    <input type="radio" id="type-author" name="fieldcode"
                                           value="<?php echo $fieldCode; ?>"/>
                                    <label for="type-author">Author</label>

                                    <?php
                                }
                                if ($searchField['Label'] == 'Title') {
                                    $fieldCode = $searchField['Code']; ?>

                                    <input type="radio" id="type-title" name="fieldcode"
                                           value="<?php echo $fieldCode; ?>"/>
                                    <label for="type-title">Title</label>

                                    <?php
                                }
                            } ?>
                        <?php } ?>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).one('pagecontainershow', function () {
        $("#lookfor").keyup(function (event) {
            if ($.trim($(this).val()).length > 0) {
                $("#submit").button("enable");
            } else {
                $("#submit").button("disable");
            }
        });
    });

</script>
