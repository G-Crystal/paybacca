<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	require_once("inc/config.inc.php");

	$q = $_GET["q"];
	if (!$q) return;

	$q = strtolower(mysql_real_escape_string($q));
	$q = substr(trim($q), 0, 100);

	$ac_result = smart_mysql_query("SELECT DISTINCT retailer_id, title, image, cashback, website FROM cashbackengine_retailers WHERE (title LIKE '$q%' OR website LIKE '%$q%') AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 20");

	if (mysql_num_rows($ac_result) > 0)
	{
?>
	<div class="ac_results">
	<?php while ($ac_row = @mysql_fetch_array($ac_result)) { ?>
		<a href="<?php echo GetRetailerLink($ac_row['retailer_id'], $ac_row['title']); ?>">
		<div class="xlist">
			 <div class="ac_image"><?php if ($ac_row['image'] != "") { ?><img src="<?php if (!stristr($ac_row['image'], 'http')) echo SITE_URL."img/"; echo $ac_row['image']; ?>" /><?php } ?></div>
			 <div class="ac_result">
				<?php echo $ac_row['title']; ?>
				<?php if ($ac_row['cashback'] != "") { ?> <span class="ac_cashback"><?php echo DisplayCashback($ac_row['cashback']); ?></span><?php } ?>
				<?php if ($ac_row['website'] != "") { ?><span class="ac_website"><?php $parse = parse_url($ac_row['website']); echo $parse['host']; ?></span><?php } ?>
			 </div>
			 <div style="clear:both"></div>
		 </div>
		 </a>
	<?php }	?>
	</div>

<?php } ?>