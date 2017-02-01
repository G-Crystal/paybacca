<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");

	$retailer_id = (int)$_GET['id'];
	$cc = 0;


	if (isset($_GET['act']) && $_GET['act'] == "add")
	{
		$check_query = smart_mysql_query("SELECT * FROM cashbackengine_favorites WHERE user_id='$userid' AND retailer_id='$retailer_id'");
		if (mysql_num_rows($check_query) == 0)
		{
			smart_mysql_query("INSERT INTO cashbackengine_favorites SET user_id='$userid', retailer_id='$retailer_id', added=NOW()");

			header("Location: myfavorites.php?msg=added");
			exit();
		}
	}

	if (isset($_GET['act']) && $_GET['act'] == "del")
	{
		$del_query = "DELETE FROM cashbackengine_favorites WHERE user_id='$userid' AND retailer_id='$retailer_id'";
		if (smart_mysql_query($del_query))
		{
			header("Location: myfavorites.php?msg=deleted");
			exit();
		}
	}

	$query = "SELECT cashbackengine_favorites.*, cashbackengine_retailers.* FROM cashbackengine_favorites cashbackengine_favorites, cashbackengine_retailers cashbackengine_retailers WHERE cashbackengine_favorites.user_id='$userid' AND cashbackengine_favorites.retailer_id=cashbackengine_retailers.retailer_id AND (cashbackengine_retailers.end_date='0000-00-00 00:00:00' OR cashbackengine_retailers.end_date > NOW()) AND cashbackengine_retailers.status='active' ORDER BY cashbackengine_retailers.title ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYFAVORITES_TITLE;

	require_once ("inc/header.inc.php");
	
?>

	<h1><?php echo CBE1_MYFAVORITES_TITLE; ?></h1>


		  <?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_msg">
				<?php
					switch ($_GET['msg'])
					{
						case "added": echo CBE1_MYFAVORITES_MSG1; break;
						case "deleted": echo CBE1_MYFAVORITES_MSG2; break;
					}
				?>
			</div>
		<?php } ?>


	<?php if ($total > 0) { ?>

			<p align="center"><?php echo CBE1_MYFAVORITES_TEXT; ?></p>
			<div class="sline"></div><br/>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="<?php echo IMAGE_WIDTH; ?>" align="center" valign="middle">
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
						</a>
						<a href="#" onclick="if (confirm('<?php echo CBE1_MYFAVORITES_DELETE; ?>') )location.href='<?php echo SITE_URL; ?>myfavorites.php?act=del&id=<?php echo $row['retailer_id']; ?>'" title="<?php echo CBE1_MYFAVORITES_DEL; ?>"><img src="<?php echo SITE_URL; ?>images/delete.png" border="0" alt="<?php echo CBE1_MYFAVORITES_DEL; ?>" /></a>
					</td>
					<td align="left" valign="middle">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td width="80%" align="left" valign="middle">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="midle">
									<?php if ($row['cashback'] != "") { ?>
										<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
										<span class="cashback"><?php echo DisplayCashback($row['cashback']); ?> <?php echo CBE1_CASHBACK; ?></span>
									<?php } ?>
									<span class="visits"><?php echo GetUserClicksTotal($userid, $row['retailer_id']); ?> <?php echo CBE1_CLICK_VISITS; ?></span>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="top" align="left">
									<div class="retailer_description"><?php echo TruncateText(stripslashes($row['description']), STORES_DESCRIPTION_LIMIT); ?>&nbsp;</div>
								</td>
							</tr>
							<tr>
								<td valign="middle" align="left">
									<?php
										$share_title = urlencode($row['title']." ".CBE1_STORE_EARN." ".DisplayCashback($row['cashback'])." ".CBE1_CASHBACK2);
										if (isLoggedIn()) $share_add .= "&ref=".(int)$_SESSION['userid'];
										$share_link = urlencode(GetRetailerLink($row['retailer_id'], $row['title']).$share_add);
									?>
									<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $share_link; ?>&t=<?php echo $share_title; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" align="absmiddle" /></a>&nbsp;
									<a href="https://twitter.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_link; ?>&via=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" align="absmiddle" /></a>&nbsp;&nbsp;
									<?php if ($row['conditions'] != "") { ?>
										<div class="cashbackengine_tooltip">
											<a class="conditions" href="#"><?php echo CBE1_CONDITIONS; ?></a> <span class="tooltip"><?php echo $row['conditions']; ?></span>
										</div>
									<?php } ?>
								</td>
								<td valign="middle" align="right">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php } ?>
           </table>

    <?php }else{ ?>
			<p align="center">
				<?php echo CBE1_MYFAVORITES_NO; ?><br/><br/>
				<a class="button" href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_MYFAVORITES_ADD; ?></a>
			</p>
     <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>