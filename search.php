<?php

/*******************************************************************\

 * CashbackEngine v3.0

 * http://www.CashbackEngine.net

 *

 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.

 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------

\*******************************************************************/



	session_start();

	require_once("inc/config.inc.php");

	require_once("inc/pagination.inc.php");

	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1))$page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}

	$cc = 0;

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
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

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
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

	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }

	$from = ($page-1)*$results_per_page;

	$where = "";

	if (isset($_GET['action']) && $_GET['action'] == "search")
	{
		$stext = mysql_real_escape_string(getGetParameter('searchtext'));
		$stext = substr(trim($stext), 0, 100);

		$where .= " (title LIKE '%".$stext."%' OR description LIKE '%".$stext."%' OR website LIKE '%".$stext."%' OR tags LIKE '%".$stext."%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";

		if ($rrorder == "cashback")
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
		else
			$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY title ASC");
	}

	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);

	$total_on_page = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SEARCH_TITLE." ".$stext;

	require_once ("inc/header.inc.php");
?>

	<h3 class="brd"><?php echo CBE1_SEARCH_TITLE; ?> '<?php echo $stext; ?>'</h3>

	<?php
		if ($total > 0) {
	?>

	<?php if (!isLoggedIn()) { ?><div class="login_msg"><?php echo CBE1_STORES_LOGIN; ?></div><?php } ?>

	<div class="browse_top">

		<div class="sortby">

			<form action="<?php echo SITE_URL; ?>search.php?<?php echo $_SERVER['QUERY_STRING']; ?>" id="form1" name="form1" method="get">
				<span><?php echo CBE1_SORT_BY; ?>:</span>
				<select name="column" id="column" onChange="document.form1.submit()">
					<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>><?php echo CBE1_SEARCH_NAME; ?></option>
					<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_SEARCH_POPULARITY; ?></option>
					<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_SEARCH_DATE; ?></option>
					<option value="cashback" <?php if ($_GET['column'] == "cashback") echo "selected"; ?>><?php echo CBE1_SEARCH_CASHBACK; ?></option>
				</select>

				<select name="order" id="order" onChange="document.form1.submit()">
					<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
					<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
				</select>

				<input type="hidden" id="searchtext" name="searchtext" value="<?php echo $stext; ?>" />
				<input type="hidden" id="page" name="page" value="<?php echo $page; ?>" />
				<input type="hidden" name="action" value="search" />
			</form>

		</div>

	</div>

	<div class="row" id="search_result_box">
	<?php while ($row = mysql_fetch_array($result)) { ?>
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
			<div class="store-box-div">
				<div class="">
					<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
				</div>
				<div class="store-icon-div">
					<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" class="store-icon-img" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
					</a>
				</div>
				<?php if ($row['cashback'] != "") { ?>
					<div class="cashback">
						<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
						<span class="value"><?php echo DisplayCashback($row['cashback']); ?></span><span class="value">Cash Back</span>
					</div>
				<?php } else { ?>
					<div class="cashback">
						<a class="common-btn" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">See More</a>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	</div>

	<div class="col-xs-12 text-center">
		<a id="browse_more_btn" class="common-btn margin-top-10">Browse More</a>
	</div>


	<?php }else{ ?>

		<p align="center"><?php echo CBE1_SEARCH_NO; ?></p>

		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a></p>

	<?php } ?>


	<script language="javascript">
	$(document).ready(function(){
		$("#browse_more_btn").click(function(){
			item = {
				"show" : '<?php echo $results_per_page?>',
				"column" : '<?php echo $rrorder?>',
				"order": '<?php echo $rorder?>',
				"page": parseInt($("#page").val()) + 1,
				"action": "search",
				"searchtext": $("#searchtext").val()
			}

			$.ajax({
				type : 'GET',
				url  : 'server/search.php',
				data : {params:JSON.stringify(item)}
			})
			.done(function(data) {
				$("#search_result_box").append(data);
				$("#page").val(parseInt($("#page").val()) + 1);
			});
		});

	});
	</script>



<?php require_once ("inc/footer.inc.php"); ?>