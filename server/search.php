<?php

/*******************************************************************\

 * Load retailers processor

\*******************************************************************/

	require_once("../inc/config.inc.php");
    
    $params = json_decode($_GET['params']);

	if (isset($params->show) && is_numeric($params->show) && $params->show > 0 && in_array($params->show, $results_on_page))
	{
		$results_per_page = (int)$params->show;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}

	////////////////// filter  //////////////////////
    if (isset($params->column) && $params->column != "")
    {
        switch ($params->column)
        {
            case "title": $rrorder = "title"; break;
            case "added": $rrorder = "added"; break;
            case "visits": $rrorder = "visits"; break;
            case "cashback": $rrorder = "cashback"; break;
            default: $rrorder = "title"; break;
        }
    }
    else
    {
        $rrorder = "title";
    }

    if (isset($params->order) && $params->order != "")
    {
        switch ($params->order)
        {
            case "asc": $rorder = "asc"; break;
            case "desc": $rorder = "desc"; break;
            default: $rorder = "asc"; break;
        }
    }
    else
    {
        $rorder = "asc";
    }


	if (isset($params->page) && is_numeric($params->page) && $params->page > 0) { $page = (int)$params->page; } else { $page = 1; }

	$from = ($page-1)*$results_per_page;

	$where = "";

	if (isset($params->action) && $params->action == "search")
	{
		$stext = mysql_real_escape_string($params->searchtext);
		$stext = substr(trim($stext), 0, 100);

		$where .= " (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%' OR website LIKE '%".$stext."%' OR tags LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";

		if ($rrorder == "cashback")
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
		else
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY title ASC");
	}

	$result = smart_mysql_query($query);

    $ret = "";

    while ($row = mysql_fetch_array($result)) {
        $ret .= "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-6'>";
        $ret .= "<div class='store-box-div'>";
        $ret .= "<div class=''>";
        $ret .= "<a class='retailer_title' href='" . GetRetailerLink($row["retailer_id"], $row["title"]) ."'>" . $row["title"] . "</a>";
        $ret .= "</div>";
        $ret .= "<div class='store-icon-div'>";
        $ret .= "<a href='" . GetRetailerLink($row["retailer_id"], $row["title"]) ."'>";
        $ret .= "<div class='imagebox'><img src='"; 
        if (!stristr($row['image'], 'http')) $ret .= SITE_URL."img/"; 
        $ret .= $row['image'] ."' class='store-icon-img' alt='" . $row["title"] ."' title='" . $row["title"] . "' border='0' /></div>";
        $ret .= "</a></div>";
        if ($row["cashback"] != "") {
            $ret .= "<div class='cashback'>";
            if ($row["old_cashback"] != "") $ret .= "<span class='old_cashback'>" . DisplayCashback($row["old_cashback"]) . "</span>";
            $ret .= "<span class='value'>" . DisplayCashback($row['cashback']) . "</span>";
            $ret .= "<span class='value'>Cash Back</span>";
            $ret .= "</div>";
        }
        $ret .= "</div>";
        $ret .= "</div>";
    }

    echo $ret;