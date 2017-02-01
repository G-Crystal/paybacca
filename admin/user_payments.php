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
	require_once("../inc/pagination.inc.php");

	$cc = 0;

	// results per page
	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0)
		$results_per_page = (int)$_GET['show'];
	else
		$results_per_page = 10;


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "created": $rrorder = "created"; break;
					case "transaction_id": $rrorder = "transaction_id"; break;
					case "reference_id": $rrorder = "reference_id"; break;
					case "payment_type": $rrorder = "payment_type"; break;
					case "amount": $rrorder = "amount"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "added"; break;
				}
			}
			else
			{
				$rrorder = "created";
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
			if (isset($_GET['filter']) && $_GET['filter'] != "")
			{
				$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
				$where .= " AND (title LIKE '%$filter%' OR code LIKE '%$filter%') ";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$uid = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS date_created FROM cashbackengine_transactions WHERE user_id='$uid' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_transactions WHERE user_id='$uid'";
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$title2 = GetUsername($uid)."'s";
	}

		$title = $title2." Payment History";
		require_once ("inc/header.inc.php");

?>

	<div id="addnew">
		<a href="money2user.php?id=<?php echo $uid; ?>" title="Transfer Money"><img src="images/addmoney.gif" border="0" alt="Transfer Money" align="absmiddle" /> Transfer Money</a>
	</div>

	<h2><?php echo $title2; ?> Payment History</h2>

	  <?php if ($total > 0) { ?>

			<form id="form1" name="form1" method="get" action="">
			<table bgcolor="#F9F9F9" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
			<td nowrap="nowrap" width="65%" valign="middle" align="left">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="created" <?php if ($_GET['column'] == "created") echo "selected"; ?>>Date</option>
			<option value="reference_id" <?php if ($_GET['column'] == "retailer_id") echo "selected"; ?>>Reference ID</option>
			<option value="payment_type" <?php if ($_GET['column'] == "payment_type") echo "selected"; ?>>Payment Type</option>
			<option value="amount" <?php if ($_GET['column'] == "amount") echo "selected"; ?>>Amount</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  &nbsp;&nbsp;View: 
          <select name="show" id="show" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
			<option value="111111111" <?php if ($_GET['show'] == "111111111") echo "selected"; ?>>ALL</option>
          </select>
			<?php if ($uid) { ?><input type="hidden" name="id" value="<?php echo $uid; ?>" /><?php } ?>
			</td>
			<td nowrap="nowrap" width="35%" valign="middle" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			</form>

            <table class="tbl" align="center" width="100%" class="brd" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="17%">Date</th>
				<th width="15%">Reference ID</th>
                <th width="30%">Payment Type</th>
				<th width="15%">Amount</th>
				<th width="13%">Status</th>
				<th width="10%">Actions</th>
              </tr>
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
                  <td valign="middle" align="center"><a href="payment_details.php?id=<?php echo $row['transaction_id']; ?>"><?php echo $row['reference_id']; ?></a></td>
                  <td valign="middle" align="left">
					<?php echo $row['retailer']; ?> 
						<?php
								switch ($row['payment_type'])
								{
									case "Cashback":			echo PAYMENT_TYPE_CASHBACK; break;
									case "Withdrawal":			echo PAYMENT_TYPE_WITHDRAWAL; break;
									case "Referral Commission": echo PAYMENT_TYPE_RCOMMISSION; break;
									case "friend_bonus":		echo PAYMENT_TYPE_FBONUS; break;
									case "signup_bonus":		echo PAYMENT_TYPE_SBONUS; break;
									default:					echo $row['payment_type']; break;
								}
						?>				
				  </td>
				  <td valign="middle" align="center"><?php echo DisplayMoney($row['amount']); ?></td>
                  <td valign="middle" align="center" style="padding-left: 15px;">
					<?php
							switch ($row['status'])
							{
								case "confirmed": echo "<span class='confirmed_status'>confirmed</span>"; break;
								case "pending": echo "<span class='pending_status'>pending</span>"; break;
								case "declined": echo "<span class='declined_status'>declined</span>"; break;
								case "failed": echo "<span class='failed_status'>failed</span>"; break;
								case "request": echo "<span class='request_status'>awaiting approval</span>"; break;
								case "paid": echo "<span class='paid_status'>paid</span>"; break;
								default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <a href=\"#\" onmouseover=\"tooltip('".$row['reason']."<br/><span class=\'tip\'></span>');\" onmouseout=\"exit();\"><img src=\"/images/info.png\" alt=\"\" align=\"absmiddle\" border=\"0\" /></a>";
							}
					?>
				  </td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="payment_details.php?id=<?php echo $row['transaction_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="payment_edit.php?id=<?php echo $row['transaction_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this payment?') )location.href='payment_delete.php?id=<?php echo $row['transaction_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
                </tr>
				<?php } ?>
				</table>

					<?php echo ShowPagination("transactions",$results_per_page,"user_payments.php?id=$uid&column=$rrorder&order=$rorder&show=$results_per_page&", "WHERE user_id='$uid'"); ?>

	  <?php }else{ ?>
			<div class="info_box">There are no transactions at this time.</div>
      <?php } ?>

	  <p align="center"><input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" /></p>

<?php require_once ("inc/footer.inc.php"); ?>