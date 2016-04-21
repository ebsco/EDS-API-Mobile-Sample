<?php
session_start();


function root()
{
    return dirname(__FILE__) . '/';
}


function render_template($locals, $fileName)
{
    extract($locals);
    ob_start();
    include(root() . 'views/' . $fileName . '.php');
    return ob_get_clean();
}


function render($fileName, $templateName, $variableArray = array())
{
    $variableArray['content'] = render_template($variableArray, $fileName);
    print render_template($variableArray, $templateName);
}


function paginate($recordCount, $limit, $page, $searchTerm, $fieldCode)
{
    $output = '';
    $linkCount = ceil($recordCount / $limit);
    if (!empty($page)) {
        if ($page > $linkCount) {
            $page = $linkCount;
        }
    } else {
        $page = 1;
    }
    $base_url = "pageOptions.php?$searchTerm&fieldcode=$fieldCode";

    $s = $page - 1;
    if ($linkCount >= 1) {
        $output = '<p>';
        if ($s > 0) {
            $output .= "<a id='previousPage' href=\"{$base_url}&pagenumber=GoToPage({$s})\"><span class='ui-pagination-prev'>Previous</span></a>";
        }
        $p_1 = $page + 1;
        if ($p_1 <= $linkCount) {
            $output .= "<a id='nextPage' href=\"{$base_url}&pagenumber=GoToPage({$p_1})\"><span class='ui-pagination-next'>Next</span></a>";
        }
        $output .= '<br class="clear" /></p>';
    }
    return $output;
}
