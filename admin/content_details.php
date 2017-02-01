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
		$cid	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(modified, '%e %b %Y %h:%i %p') AS modify_date FROM cashbackengine_content WHERE content_id='$cid'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}

	$title = "View Content";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

          <h2>View Content</h2>

          <table width="100%" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
			<td bgcolor="#F9F9F9" align="left" valign="top">
				<h1 style="margin:0;padding: 3px;"><?php echo stripslashes($row['title']); ?></h1>
			</td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
          <tr>
            <td valign="top"><?php echo stripslashes($row['description']); ?></td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
			<?php if ($row['meta_description'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><?php echo $row['meta_description']; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['meta_keywords'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><?php echo $row['meta_keywords']; ?></td>
			</tr>
			<?php } ?>
          <tr>
            <td align="right" valign="top">
				<?php if ($row['language'] != "") { ?>Language: <b><?php echo $row['language']; ?></b> | <?php } ?>
				Last modified: <span class="date"><?php echo $row['modify_date']; ?></span>
			</td>
          </tr>
		  <?php if ($row['name'] == "page") { ?>
          <tr>
            <td colspan="2" height="30" bgcolor="#F9F9F9" style="line-height: 17px;" align="left" valign="middle">
				<?php if ($row['page_location'] != "") { ?>
					<b>Page location</b>: 
					<?php 
						switch ($row['page_location'])
						{
							case "top": echo "Top menu"; break;
							case "footer": echo "Footer menu"; break;
							case "topfooter": echo "Top &amp; footer"; break;
							default: echo "---------"; break;
						}
					?><br/>			
				<?php } ?>
				<?php if ($row['link_title'] != "") { ?><b>Link title</b>: <?php echo $row['link_title']; ?><br/><?php } ?>
				<b>Page URL</b>: <a target="_blank" href="<?php echo SITE_URL."content.php?id=".$row['content_id']; ?>"><?php echo SITE_URL."content.php?id=".$row['content_id']; ?></a>
			</td>
          </tr>
		  <?php } ?>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="button" class="submit" name="edit" value="Edit Page" onClick="javascript:document.location.href='content_edit.php?id=<?php echo $row['content_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='content.php'" />
            </td>
          </tr>
          </table>

      <?php }else{ ?>
			<p align="center">Sorry, no page found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>