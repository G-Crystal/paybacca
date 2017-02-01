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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$news_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: news.php");
		exit();
	}

	$result = smart_mysql_query("SELECT *, DATE_FORMAT(added, '".DATE_FORMAT."') AS news_date FROM cashbackengine_news WHERE news_id='$news_id' AND status='active' LIMIT 1");
	$total = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_NEWS_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_NEWS_TITLE; ?></h1>

	<?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<div class="breadcrumbs"><a href="<?php echo SITE_URL; ?>" class="home_link"><?php echo CBE1_BREADCRUMBS_HOME; ?></a> &#155; <a href="<?php echo SITE_URL; ?>news.php"><?php echo CBE1_NEWS_TITLE; ?></a> &#155; <?php echo $row['news_title']; ?></div>
	
		<div class="news_date"><?php echo $row['news_date']; ?></div>
		<div class="news_title"><?php echo $row['news_title']; ?></div>
		<div class="news_description"><?php echo stripslashes($row['news_description']); ?></div>
		<p align="right"><a class="more" href="<?php echo SITE_URL; ?>news.php"><?php echo CBE1_NEWS_OTHER; ?></a></p>
	
	<?php }else{ ?>
	
			<p align="center"><?php echo CBE1_NEWS_NOT_FOUND; ?></p>
			<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>news.php"><?php echo CBE1_GO_BACK; ?></a></p>
	
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>