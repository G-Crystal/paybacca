<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$news_id = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS modify_date FROM cashbackengine_news WHERE news_id='$news_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}

	$title = "News Details";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

          <h2>News Details</h2>

          <table width="100%" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td align="left" valign="top">
				<b><?php echo stripslashes($row['news_title']); ?></b>
            </td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
          <tr>
            <td valign="top"><?php echo stripslashes($row['news_description']); ?></td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
          <tr>
            <td align="right" valign="top">Last modified: <span class="date"><?php echo $row['modify_date']; ?></span></td>
          </tr>
          <tr>
            <td align="center" valign="bottom">
			<input type="button" class="submit" name="edit" value="Edit News" onClick="javascript:document.location.href='news_edit.php?id=<?php echo $row['news_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
			<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='news.php'" />
		  </td>
          </tr>

          </table>

      <?php }else{ ?>
				<div class="info_box">Sorry, no news found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>