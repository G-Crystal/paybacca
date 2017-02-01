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

	header("Content-Type: text/xml;charset=UTF-8");
   
	$query = "SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY added DESC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		echo '<?xml version="1.0" encoding="UTF-8"?> 
			<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		while ($row = mysql_fetch_array($result))
		{  
			$i_url	= GetRetailerLink($row['retailer_id'], $row['title']);
			$year	= substr($row['added'],0,4);
			$month  = substr($row['added'],5,2);
			$day	= substr($row['added'],8,2);
			$i_date = ''.$year.'-'.$month.'-'.$day.'';

			echo  
			'
			<url>
			<loc>'.$i_url.'</loc>
			<lastmod>'.$i_date.'</lastmod>
			<changefreq>daily</changefreq>
			<priority>0.8</priority>
			</url>
			';
		}

		echo  
		'</urlset>'; 
	}

?>