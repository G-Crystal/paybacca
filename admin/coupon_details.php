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
		$pn			= (int)$_GET['pn'];
		$couponid	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added, DATE_FORMAT(last_visit, '%e %b %Y %h:%i %p') AS last_used, DATE_FORMAT(start_date, '%e %b %Y %h:%i') AS coupon_start_date, DATE_FORMAT(end_date, '%e %b %Y %h:%i') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE coupon_id='$couponid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		smart_mysql_query("UPDATE cashbackengine_coupons SET viewed='1' WHERE coupon_id='$couponid' AND viewed='0'");
	}


	$title = "Coupon Details";
	require_once ("inc/header.inc.php");

?>
    
     <h2>Coupon Details</h2>

	  <?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<img src="images/icons/scissors.png" style="position: absolute; right: 150px; top: 18px;" />
        <table bgcolor="#F9F9F9" style="border: 1px dashed #eee;" width="100%" cellpadding="3" cellspacing="3"  border="0" align="center">
			<?php if ($row['exclusive'] == 1) { ?>
			<tr>
				<td colspan="2" align="right" align="right" valign="top"><img src="images/icons/featured.png" align="absmiddle" /> <span style="color:#EF8407;">Exclusive  Coupon</span></td>
			</tr>
			<?php } ?>
			<?php if ($row['coupon_type'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Offer Type:</td>
				<td valign="top">
					<b>
					<?php
							switch ($row['coupon_type'])
							{
								case "coupon": echo "Coupon Code"; break;
								case "printable": echo "Printable Coupon"; break;
								case "discount": echo "Online Discount/Sale"; break;
								default: echo $row['coupon_type']; break;
							}
					?>
					</b>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td width="45%" valign="middle" align="right" class="tb1">Title:</td>
				<td valign="top"><b><?php echo $row['title']; ?></b></td>
			</tr>
			<?php if ($row['retailer_id'] > 0) { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Store:</td>
				<td valign="top"><a href="retailer_details.php?id=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreName($row['retailer_id']); ?></a></td>
			</tr>
			<?php } ?>
			<?php if ($row['code'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Coupon Code:</td>
				<td valign="top"><?php echo $row['code']; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['link'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Link:</td>
				<td valign="top"><?php echo $row['link']; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['start_date'] != "0000-00-00 00:00:00") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Start Date:</td>
				<td valign="top"><?php echo $row['coupon_start_date']; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Expiry Date:</td>
				<td valign="top"><?php echo $row['coupon_end_date']; ?></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="tb1">Expires in:</td>
				<td valign="top"><?php if ($row['end_date'] != "0000-00-00 00:00:00") { echo GetTimeLeft($row['time_left']); }else{ echo "----"; } ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['description'] != "") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Description:</td>
				<td valign="top"><?php echo $row['description']; ?></td>
            </tr>
			<?php } ?>
			<tr>
				<td valign="top" align="right" class="tb1">Sort Order:</td>
				<td valign="top"><?php echo $row['sort_order']; ?></td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">Visits:</td>
				<td valign="top"><?php echo number_format($row['visits']); ?></td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">Visits Today:</td>
				<td valign="top"><?php echo number_format($row['visits_today']); ?></td>
            </tr>
			<?php if ($row['last_visit'] != "0000-00-00 00:00:00") { ?>
			<tr>
				<td valign="top" align="right" class="tb1">Last Used:</td>
				<td valign="top"><?php echo $row['last_used']; ?></td>
            </tr>
			<?php } ?>
			<tr>
				<td valign="top" align="right" class="tb1">Submitted by:</td>
				<td valign="top">
					<?php if ($row['user_id'] == "0") { ?>
						Admin
					<?php }elseif ($row['user_id'] == "11111111") { ?>
						Anonym
					<?php }elseif ($row['user_id'] > 0) { ?>
						<a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a>
					<?php } ?>
				</td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">Date Added:</td>
				<td valign="top"><?php echo $row['date_added']; ?></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Status:</td>
				<td valign="top">
				<?php
						switch ($row['status'])
						{
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							case "expired": echo "<span class='expired_status' style='margin: 0;'>".$row['status']."</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
				?>
				</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="submit" name="edit" value="Edit Coupon" onClick="javascript:document.location.href='coupon_edit.php?id=<?php echo $row['coupon_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='coupons.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
			  </td>
            </tr>
          </table>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no coupon found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>