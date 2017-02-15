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

		$pn		= (int)$_GET['pn'];

		$rid	= (int)$_GET['id'];



		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added, DATE_FORMAT(end_date, '%e %b %Y %h:%i') AS retailer_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_retailers WHERE retailer_id='$rid' LIMIT 1";

		$result = smart_mysql_query($query);

		$total = mysql_num_rows($result);

	}





	$title = "Retailer Details";

	require_once ("inc/header.inc.php");



?>

    

     <h2>Retailer Details</h2>



	 <?php if ($total > 0) {

	

		 $row = mysql_fetch_array($result);



	 ?>



		<table bgcolor="#F9F9F9" width="100%" cellpadding="3" cellspacing="3"  border="0" align="center">

			<tr>

				<td colspan="2" align="right" valign="top">

					<?php if ($row['featured'] == 1) { ?><div style="float: right;"><img src="images/icons/featured.png" /> <span style="color:#F9C107;">Featured</span></div><?php } ?>

					<?php if ($row['deal_of_week'] == 1) { ?><div style="float: right; margin-right: 10px;"><img src="images/icons/deal_of_week.png"  /> <span style="color:#777777;">Store of the Week</span></div><?php } ?>

				</td>

			</tr>

			<tr>

				<td width="30%" valign="middle" align="right" class="tb1">Title:</td>

				<td width="70%" valign="top"><b><?php echo $row['title']; ?></b></td>

			</tr>

			<?php if ($row['network_id'] > 0) { ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Affiliate Network:</td>

				<td valign="top"><?php echo GetNetworkName($row['network_id']); ?></td>

			</tr>

			<tr>

				<td valign="middle" align="right" class="tb1">Program ID:</td>

				<td valign="top"><?php echo $row['program_id']; ?></td>

			</tr>

			<?php } ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Category:</td>

				<td valign="top"><?php echo GetRetailerCategory($row['retailer_id']); ?></td>

			</tr>

			<?php if (GetStoreCountries($row['retailer_id']) != "") { ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Country:</td>

				<td valign="top"><?php echo GetStoreCountries($row['retailer_id']); ?></td>

			</tr>

			<?php } ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Image:</td>

				<td align="left" valign="top"><img src="<?php if (!stristr($row['image'], 'http')) echo "/img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" align="left" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" class="imgs" border="0" /></td>

			</tr>

			<?php if (stristr($row['image'], 'http')) { ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Image URL:</td>

				<td valign="top"><a href="<?php echo $row['image']; ?>" target="_blank"><?php echo $row['image']; ?></a></td>

			</tr>

			<?php } ?>

			<tr>

				<td valign="middle" align="right" class="tb1">URL:</td>

				<td valign="top"><input type="text" class="textbox" size="95" style="border: 1px solid #EEE; padding: 5px 2px; background: #F3FEFF;" value="<?php echo $row['url']; ?>" /></td>

			</tr>

			<?php if ($row['cashback'] != "") { ?>

			<tr>

				<td valign="middle" align="right" class="tb1">Cash Back:</td>

				<td valign="top">

					<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>

					<span class="cashback"><?php echo DisplayCashback($row['cashback']); ?></span></td>

			</tr>

			<?php } ?>

			<?php if ($row['description'] != "") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">Description:</td>

				<td valign="top"><?php echo stripslashes($row['description']); ?></td>

            </tr>

			<?php } ?>

			<?php if ($row['conditions'] != "") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">Conditions:</td>

				<td valign="top"><?php echo $row['conditions']; ?></td>

			</tr>

			<?php } ?>

			<?php if ($row['tags'] != "") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">Tags:</td>

				<td valign="top"><?php echo $row['tags']; ?></td>

			</tr>

			<?php } ?>

			<?php if ($row['website'] != "") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">Website:</td>

				<td valign="top"><?php echo $row['website']; ?></td>

			</tr>

			<?php } ?>

			<?php if ($row['seo_title'] != "") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">SEO Title:</td>

				<td valign="top"><?php echo $row['seo_title']; ?></td>

			</tr>

			<?php } ?>

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

			<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>

			<tr>

				<td valign="top" align="right" class="tb1">Expiry Date:</td>

				<td valign="top"><?php echo $row['retailer_end_date']; ?></td>

			</tr>

			<tr>

				<td valign="top" align="right" class="tb1">Expires in:</td>

				<td valign="top"><?php if ($row['end_date'] != "0000-00-00 00:00:00") { echo GetTimeLeft($row['time_left']); }else{ echo "----"; } ?></td>

			</tr>

			<?php } ?>

			<tr>

				<td valign="top" align="right" class="tb1">Coupons:</td>

				<td valign="top"><a href="coupons.php?store=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a></td>

            </tr>

			<tr>

				<td valign="top" align="right" class="tb1">Reviews:</td>

				<td valign="top"><a href="reviews.php?store=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreReviewsTotal($row['retailer_id'], $all = 1, $word = 0); ?></a></td>

            </tr>

			<tr>

				<td valign="top" align="right" class="tb1">Rating:</td>

				<td valign="top"><?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?></td>

            </tr>

			<tr>

				<td valign="top" align="right" class="tb1">Visits:</td>

				<td valign="top"><a href="clicks.php?store=<?php echo $row['retailer_id']; ?>"><?php echo number_format($row['visits']); ?></a></td>

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

								case "expired": echo "<span class='expired_status'>".$row['status']."</span>"; break;

								default: echo "<span class='default_status'>".$row['status']."</span>"; break;

							}

						?>

				</td>

            </tr>

            <tr>

              <td align="center" colspan="2" valign="bottom">

				<input type="button" class="submit" name="edit" value="Edit Retailer" onClick="javascript:document.location.href='retailer_edit.php?id=<?php echo $row['retailer_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 

				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='retailers.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />

			  </td>

            </tr>

          </table>

      

	  <?php }else{ ?>

			<p align="center">Sorry, no retailer found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>

      <?php } ?>



<?php require_once ("inc/footer.inc.php"); ?>