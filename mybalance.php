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
	require_once("inc/pagination.inc.php");

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_BALANCE_TITLE;

	require_once ("inc/header.inc.php");

?>

		<h1><?php echo CBE1_BALANCE_TITLE; ?></h1>

		<div class="abalance">
			<?php echo CBE1_WITHDRAW_BALANCE; ?><br/>
			<span><?php echo GetUserBalance($userid); ?></span>
			<?php if (GetUserBalance($userid, 1) > MIN_PAYOUT) { ?><br/><br/><a href="<?php echo SITE_URL; ?>withdraw.php" class="submit"><?php echo CBE1_WITHDRAW_BUTTON; ?></a><?php } ?>
		</div>

		<table align="center" class="btb" width="300" border="0" cellspacing="0" cellpadding="7">
		<tr class="available_balance">
			<td width="200" valign="middle" align="left"><?php echo CBE1_BALANCE_ABALANCE; ?></td>
			<td valign="middle" align="right"><?php echo GetUserBalance($userid); ?></td>
		</tr>
		<tr class="pending_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_PCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo GetPendingBalance(); ?></td>
		</tr>
		<tr class="declined_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_DCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo GetDeclinedBalance(); ?></td>
		</tr>
		<tr class="cashout_requested">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_CREQUESTED; ?></td>
			<td valign="middle" align="right"><?php echo GetCashOutRequested(); ?></td>
		</tr>
		<tr class="cashout_processed">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_CPROCESSED; ?></td>
			<td valign="middle" align="right"><?php echo GetCashOutProcessed(); ?></td>
		</tr>
		<tr class="lifetime_cashback">
			<td valign="middle" align="left"><?php echo CBE1_BALANCE_LCASHBACK; ?></td>
			<td valign="middle" align="right"><?php echo GetLifetimeCashback(); ?></td>
		</tr>
		</table>

		<?php if (GetBalanceUpdateDate($userid)) { ?>
			<p align="center"<?php echo CBE1_BALANCE_TEXT2; ?> <?php echo GetBalanceUpdateDate($userid); ?></p>
		<?php } ?>


     <?php

		$cc = 0;

		$results_per_page = 10;

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(created, '".DATE_FORMAT."') AS date_created, DATE_FORMAT(updated, '".DATE_FORMAT."') AS updated_date FROM cashbackengine_transactions WHERE user_id='$userid' AND retailer!='' AND status!='unknown' ORDER BY created DESC LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_transactions WHERE user_id='$userid' AND retailer!='' AND status!='unknown' ORDER BY created DESC");
		$total = mysql_num_rows($total_result);

		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

     ?>

			<h3><?php echo CBE1_BALANCE_TITLE2; ?></h3>

            <table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="15%"><?php echo CBE1_BALANCE_DATE; ?></th>
				<th width="17%"><?php echo CBE1_PAYMENTS_ID; ?></th>
				<th width="50%"><?php echo CBE1_BALANCE_STORE; ?></th>
                <th width="15%"><?php echo CBE1_BALANCE_CASHBACK; ?></th>
                <th width="20%"><?php echo CBE1_BALANCE_STATUS; ?></th>
              </tr>
			<?php if ($total > 0) { ?>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
				  <td valign="middle" align="center"><?php echo $row['reference_id']; ?></td>
                  <td valign="middle" align="left"><?php echo ($row['retailer'] != "") ? $row['retailer'] : "-----"; ?></td>
                  <td valign="middle" align="center"><?php echo DisplayMoney($row['amount']); ?></td>
                  <td valign="middle" align="center">
					<?php
							switch ($row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default: echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$row['reason']."</span></div>";
							}
					?>
				  </td>
                </tr>
			<?php } ?>
           
					<?php echo ShowPagination("transactions",$results_per_page,"mybalance.php?","WHERE user_id='$userid' AND retailer!='' AND status!='unknown'"); ?>
			
			<?php }else{ ?>
				<tr height="30"><td colspan="5" align="center" valign="middle"><?php echo CBE1_PAYMENTS_NO; ?></td></tr>
			<?php } ?>
		   </table>


		<?php

		// cancel pending withdrawal request
		if (isset($_GET['act']) && $_GET['act'] == "cancel" && CANCEL_WITHDRAWAL == 1)
		{
			$transaction_id = (int)$_GET['id'];
			smart_mysql_query("DELETE FROM cashbackengine_transactions WHERE user_id='$userid' AND transaction_id='$transaction_id' AND payment_type='Withdrawal' AND status='request'");
			header("Location: mybalance.php?msg=cancelled");
			exit();
		}

		$cc = 0;

		$results_per_page = 10;

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(created, '".DATE_FORMAT."') AS date_created, DATE_FORMAT(process_date, '".DATE_FORMAT."') AS process_date FROM cashbackengine_transactions WHERE user_id='$userid' AND retailer='' AND status!='unknown' ORDER BY created DESC LIMIT $from, $results_per_page";

		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_transactions WHERE user_id='$userid' AND retailer='' AND status!='unknown' ORDER BY created DESC");
		$total = mysql_num_rows($total_result);

		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		?>
			<br/>
			<h3><?php echo CBE1_PAYMENTS_TITLE; ?></h3>

            <table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
                <th width="15%"><?php echo CBE1_PAYMENTS_DATE; ?></th>
				<th width="17%"><?php echo CBE1_PAYMENTS_ID; ?></th>
                <th width="22%"><?php echo CBE1_PAYMENTS_TYPE; ?></th>
				<th width="15%"><?php echo CBE1_PAYMENTS_AMOUNT; ?></th>
				<th width="17%"><?php echo CBE1_PAYMENTS_PDATE; ?></th>
				<th width="10%"><?php echo CBE1_PAYMENTS_STATUS; ?></th>
				<th width="10%"></th>
              </tr>
			<?php if ($total > 0) { ?>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
                  <td valign="middle" align="center"><?php echo $row['date_created']; ?></td>
                  <td valign="middle" align="center"><?php echo $row['reference_id']; ?></td>
                  <td valign="middle" align="center">
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
				  <td valign="middle" align="center"><?php echo $row['process_date']; ?></td>
                  <td nowrap="nowrap" valign="middle" align="center">
					<?php
							switch ($row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default:			echo "<span class='payment_status'>".$row['status']."</span>"; break;
							}

							if ($row['status'] == "declined" && $row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$row['reason']."</span></div>";
							}
					?>
				  </td>
				  <td nowrap="nowrap" valign="middle" align="center">
					<a href="<?php echo SITE_URL; ?>mybalance.php?id=<?php echo $row['transaction_id']; if ($page > 1) echo "&page=".$page; ?>#details" id="show_payment"><img src="<?php echo SITE_URL; ?>images/icon_view.png" /></a>
					<?php if (CANCEL_WITHDRAWAL == 1 && $row['payment_type'] == "Withdrawal" && $row['status'] == "request") { ?>
					<a href="#" onclick="if (confirm('<?php echo CBE1_PAYMENTS_CANCEL_MSG; ?>') )location.href='<?php echo SITE_URL; ?>mybalance.php?id=<?php echo $row['transaction_id']; ?>&act=cancel'" title="<?php echo CBE1_PAYMENTS_CANCEL; ?>"><img src="<?php echo SITE_URL; ?>images/cancel.png" border="0" alt="<?php echo CBE1_PAYMENTS_CANCEL; ?>" /></a>
					<?php } ?>
				  </td>
                </tr>
				<?php } ?>

					<?php echo ShowPagination("transactions",$results_per_page,"mybalance.php?","WHERE user_id='$userid' AND retailer='' AND status!='unknown'"); ?>

			<?php }else{ ?>
				<tr height="30"><td colspan="7" align="center" valign="middle"><?php echo CBE1_PAYMENTS_NO; ?></td></tr>
			<?php } ?>
			 </table>


		<a name="details"></a>
		<?php
	
		// payment details //
		if (isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$transaction_id = (int)$_GET['id'];
			$payment_result = smart_mysql_query("SELECT *, DATE_FORMAT(created, '".DATE_FORMAT." %h:%i %p') AS date_created, DATE_FORMAT(process_date, '".DATE_FORMAT." %h:%i %p') AS process_date FROM cashbackengine_transactions WHERE transaction_id='$transaction_id' AND user_id='$userid' AND retailer='' AND status<>'unknown' LIMIT 1");
			
			if (mysql_num_rows($payment_result) > 0)
			{
				$payment_row = mysql_fetch_array($payment_result);
		?>
		 <div id="payment_info"> 
		 <br/>
		 <div style="float: right; margin-top: 20px"><a id="hide_payment" href="javascript:void(0)"><img src="<?php echo SITE_URL; ?>images/icon_hide.png" /></a></div>
		 <h3><?php echo CBE1_PAYMENTS_DETAILS; ?> #<?php echo $payment_row['reference_id']; ?></h3>
		 
		 <div class="payment_details">
		 <table width="100%" cellpadding="5" cellspacing="3" border="0">
           <tr>
            <td width="25%" nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_TYPE; ?>:</td>
            <td align="left" valign="middle">
				<?php

						switch ($payment_row['payment_type'])
						{
							case "Cashback":			echo PAYMENT_TYPE_CASHBACK; break;
							case "Withdrawal":			echo PAYMENT_TYPE_WITHDRAWAL; break;
							case "Referral Commission": echo PAYMENT_TYPE_RCOMMISSION; break;
							case "friend_bonus":		echo PAYMENT_TYPE_FBONUS; break;
							case "signup_bonus":		echo PAYMENT_TYPE_SBONUS; break;
							default:					echo $payment_row['payment_type']; break;
						}
				?>
				<?php if ($payment_row['ref_id'] > 0) { ?> &nbsp; <span class="user"><?php echo GetUsername($payment_row['ref_id'], $hide_lastname = 1); ?></span><?php } ?>
			</td>
          </tr>
		<?php if ($payment_row['payment_type'] == "Withdrawal" && $payment_row['payment_method'] != "") { ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_METHOD; ?>:</td>
            <td align="left" valign="middle">
					<?php if ($payment_row['payment_method'] == "paypal") { ?><img src="<?php echo SITE_URL; ?>images/icon_paypal.png" align="absmiddle" />&nbsp;<?php } ?>
					<?php echo GetPaymentMethodByID($payment_row['payment_method']); ?>
			</td>
          </tr>
		<?php } ?>
		<?php if ($payment_row['payment_details'] != "") { ?>
           <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_DETAILS; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['payment_details']; ?></td>
          </tr>
		 <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_AMOUNT; ?>:</td>
            <td align="left" valign="middle"><?php echo DisplayMoney($payment_row['amount']); ?></td>
          </tr>
		<?php if ($payment_row['payment_type'] == "Withdrawal" && $payment_row['transaction_commision'] != "0.0000") { ?>
          <tr>
             <td valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_COMMISSION; ?>:</td>
             <td align="left" valign="middle"><?php echo DisplayMoney($payment_row['transaction_commision']); ?></td>
          </tr>
          <tr>
             <td valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_TAMOUNT; ?>:</td>
             <td align="left" valign="middle"><b><?php echo DisplayMoney($payment_row['amount']-$payment_row['transaction_commision']); ?></b></td>
          </tr>
	    <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_DATE; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['date_created']; ?></td>
          </tr>
		  <?php if ($payment_row['process_date'] != "") { ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_PDATE; ?>:</td>
            <td align="left" valign="middle"><?php echo $payment_row['process_date']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="left" class="tb1"><?php echo CBE1_PAYMENTS_STATUS; ?>:</td>
            <td align="left" valign="middle">
					<?php
							switch ($payment_row['status'])
							{
								case "confirmed":	echo "<span class='confirmed_status'>".STATUS_CONFIRMED."</span>"; break;
								case "pending":		echo "<span class='pending_status'>".STATUS_PENDING."</span>"; break;
								case "declined":	echo "<span class='declined_status'>".STATUS_DECLINED."</span>"; break;
								case "failed":		echo "<span class='failed_status'>".STATUS_FAILED."</span>"; break;
								case "request":		echo "<span class='request_status'>".STATUS_REQUEST."</span>"; break;
								case "paid":		echo "<span class='paid_status'>".STATUS_PAID."</span>"; break;
								default:			echo "<span class='payment_status'>".$payment_row['status']."</span>"; break;
							}

							if ($payment_row['status'] == "declined" && $payment_row['reason'] != "")
							{
								echo " <div class='cashbackengine_tooltip'><img src='".SITE_URL."images/info.png' align='absmiddle' /><span class='tooltip'>".$payment_row['reason']."</span></div>";
							}
					?>				
			</td>
          </tr>
          </table>
		  </div>
		  </div>
		<?php
			}
		} // end payment details
		?>

		<script type="text/javascript">
		$("#hide_payment").click(function () {
		  $("#payment_info").hide();
		});
		$("#show_payment").click(function () {
		  $("#payment_info").show('fast');
		});
		</script>

<?php require_once ("inc/footer.inc.php"); ?>