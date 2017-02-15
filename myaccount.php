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



	///////////////  Page config  ///////////////

	$PAGE_TITLE = CBE1_ACCOUNT_TITLE;



	require_once ("inc/header.inc.php");



?>



	<h3 class="brd"><?php echo CBE1_ACCOUNT_TITLE; ?></h3>



	<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>

	<div class="success_msg" style="width: 92%">

		<?php if ($_GET['msg'] == "welcome") { ?><?php echo CBE1_ACCOUNT_MSG; ?><?php } ?>

	</div>

	<?php } ?>

	<div class="row">
		<div class="col-xs-12">
			<?php echo str_replace("%username%",$_SESSION['FirstName'],CBE1_ACCOUNT_WELCOME); ?>
			<?php if (GetUserBalance($userid, 1) == 0) { ?><p><?php echo str_replace("%amount%",DisplayMoney("0.00"),CBE1_ACCOUNT_MSG2); ?></p><?php } ?>
		</div>
	</div>


	<h3 class="brd"><?php echo CBE1_BALANCE_TITLE; ?></h3>

	<div class="">
		<div class="col-sm-9 col-xs-12">
			<div class="row available_balance">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_ABALANCE; ?></div>
				<div class="col-xs-3"><?php echo GetUserBalance($userid); ?></div>
			</div>
			<div class="row pending_cashback">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_PCASHBACK; ?></div>
				<div class="col-xs-3"><?php echo GetPendingBalance(); ?></div>
			</div>
			<div class="row declined_cashback">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_DCASHBACK; ?></div>
				<div class="col-xs-3"><?php echo GetDeclinedBalance(); ?></div>
			</div>
			<div class="row cashout_requested">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_CREQUESTED; ?></div>
				<div class="col-xs-3"><?php echo GetCashOutRequested(); ?></div>
			</div>
			<div class="row cashout_processed">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_CPROCESSED; ?></div>
				<div class="col-xs-3"><?php echo GetCashOutProcessed(); ?></div>
			</div>
			<div class="row lifetime_cashback">
				<div class="col-xs-9"><?php echo CBE1_BALANCE_LCASHBACK; ?></div>
				<div class="col-xs-3"><?php echo GetLifetimeCashback(); ?></div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-12">
			<div class="abalance">
				<?php echo CBE1_WITHDRAW_BALANCE; ?><br/>
				<span><?php echo GetUserBalance($userid); ?></span>
				<?php if (GetUserBalance($userid, 1) > MIN_PAYOUT) { ?><br/><br/><a href="<?php echo SITE_URL; ?>withdraw.php" class="submit"><?php echo CBE1_WITHDRAW_BUTTON; ?></a><?php } ?>
			</div>
		</div>
	</div>
	<div class="row text-center">
		<?php if (GetBalanceUpdateDate($userid)) { ?>
			<p align="center"<?php echo CBE1_BALANCE_TEXT2; ?> <?php echo GetBalanceUpdateDate($userid); ?></p>
		<?php } ?>
	</div>



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

	<h3 class="brd"><?php echo CBE1_BALANCE_TITLE2; ?></h3>

	<div class="text-center">
		<div class="row">
			<div class="col-xs-2"><?php echo CBE1_BALANCE_DATE; ?></div>
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_ID; ?></div>
			<div class="col-xs-4"><?php echo CBE1_BALANCE_STORE; ?></div>
			<div class="col-xs-2"><?php echo CBE1_BALANCE_CASHBACK; ?></div>
			<div class="col-xs-2"><?php echo CBE1_BALANCE_STATUS; ?></div>
		</div>

		<?php if ($total > 0) { ?>
		<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
			<div class="row <?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<div class="col-xs-2"><?php echo $row['date_created']; ?></div>
				<div class="col-xs-2"><?php echo $row['reference_id']; ?></div>
				<div class="col-xs-4"><?php echo ($row['retailer'] != "") ? $row['retailer'] : "-----"; ?></div>
				<div class="col-xs-2"><?php echo DisplayMoney($row['amount']); ?></div>
				<div class="col-xs-2">
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
				</div>
			</div>
		<?php } ?>
			<?php echo ShowPagination("transactions",$results_per_page,"myaccount.php?","WHERE user_id='$userid' AND retailer!='' AND status!='unknown'"); ?>
		<?php }else{ ?>
			<div class="row text-center">
				<div class="col-xs-12"><?php echo CBE1_PAYMENTS_NO; ?></div>
			</div>
		<?php } ?>
	</div>




	<?php
		// cancel pending withdrawal request
		if (isset($_GET['act']) && $_GET['act'] == "cancel" && CANCEL_WITHDRAWAL == 1)
		{
			$transaction_id = (int)$_GET['id'];
			smart_mysql_query("DELETE FROM cashbackengine_transactions WHERE user_id='$userid' AND transaction_id='$transaction_id' AND payment_type='Withdrawal' AND status='request'");
			header("Location: myaccount.php?msg=cancelled");
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

	<h3 class="brd"><?php echo CBE1_PAYMENTS_TITLE; ?></h3>

	<div class="text-center">
		<div class="row">
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_DATE; ?></div>
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_ID; ?></div>
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_TYPE; ?></div>
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_AMOUNT; ?></div>
			<div class="col-xs-2"><?php echo CBE1_PAYMENTS_PDATE; ?></div>
			<div class="col-xs-1"><?php echo CBE1_PAYMENTS_STATUS; ?></div>
			<div class="col-xs-1"></div>
		</div>

		<?php if ($total > 0) { ?>
		<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
			<div class="row <?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<div class="col-xs-2"><?php echo $row['date_created']; ?></div>
				<div class="col-xs-2"><?php echo $row['reference_id']; ?></div>
				<div class="col-xs-2">
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
				</div>
				<div class="col-xs-2"><?php echo DisplayMoney($row['amount']); ?></div>
				<div class="col-xs-2"><?php echo $row['process_date']; ?></div>
				<div class="col-xs-2">
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
				</div>
				<div class="col-xs-2">
					<a href="<?php echo SITE_URL; ?>myaccount.php?id=<?php echo $row['transaction_id']; if ($page > 1) echo "&page=".$page; ?>#details" id="show_payment"><img src="<?php echo SITE_URL; ?>images/icon_view.png" /></a>
					<?php if (CANCEL_WITHDRAWAL == 1 && $row['payment_type'] == "Withdrawal" && $row['status'] == "request") { ?>
						<a href="#" onclick="if (confirm('<?php echo CBE1_PAYMENTS_CANCEL_MSG; ?>') )location.href='<?php echo SITE_URL; ?>myaccount.php?id=<?php echo $row['transaction_id']; ?>&act=cancel'" title="<?php echo CBE1_PAYMENTS_CANCEL; ?>"><img src="<?php echo SITE_URL; ?>images/cancel.png" border="0" alt="<?php echo CBE1_PAYMENTS_CANCEL; ?>" /></a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
			<?php echo ShowPagination("transactions",$results_per_page,"myaccount.php?","WHERE user_id='$userid' AND retailer='' AND status!='unknown'"); ?>
		<?php }else{ ?>
			<div class="row text-center">
				<div class="col-xs-12"><?php echo CBE1_PAYMENTS_NO; ?></div>
			</div>
		<?php } ?>
	</div>



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

		<h3 class="brd"><?php echo CBE1_PAYMENTS_DETAILS; ?> #<?php echo $payment_row['reference_id']; ?></h3>

		<div class="payment_details text-center">
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_TYPE; ?></div>
				<div class="col-xs-9">
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
				</div>
			</div>

			<?php if ($payment_row['payment_type'] == "Withdrawal" && $payment_row['payment_method'] != "") { ?>
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_METHOD; ?></div>
				<div class="col-xs-9">
					<?php if ($payment_row['payment_method'] == "paypal") { ?><img src="<?php echo SITE_URL; ?>images/icon_paypal.png" align="absmiddle" />&nbsp;<?php } ?>
					<?php echo GetPaymentMethodByID($payment_row['payment_method']); ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($payment_row['payment_details'] != "") { ?>
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_DETAILS; ?></div>
				<div class="col-xs-9"><?php echo $payment_row['payment_details']; ?></div>
			</div>
			<?php } ?>

			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_AMOUNT; ?></div>
				<div class="col-xs-9"><?php echo DisplayMoney($payment_row['amount']); ?></div>
			</div>

			<?php if ($payment_row['payment_type'] == "Withdrawal" && $payment_row['transaction_commision'] != "0.0000") { ?>
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_COMMISSION; ?></div>
				<div class="col-xs-9"><?php echo DisplayMoney($payment_row['transaction_commision']); ?></div>
			</div>
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_TAMOUNT; ?></div>
				<div class="col-xs-9"><b><?php echo DisplayMoney($payment_row['amount']-$payment_row['transaction_commision']); ?></b></div>
			</div>
			<?php } ?>

			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_DATE; ?></div>
				<div class="col-xs-9"><?php echo $payment_row['date_created']; ?></div>
			</div>

			<?php if ($payment_row['process_date'] != "") { ?>
			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_PDATE; ?></div>
				<div class="col-xs-9"><?php echo $payment_row['process_date']; ?></div>
			</div>
			<?php } ?>

			<div class="row">
				<div class="col-xs-3"><?php echo CBE1_PAYMENTS_STATUS; ?></div>
				<div class="col-xs-9">
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
				</div>
			</div>

		</div>
	</div>

	<?php
		}
	} // end payment details
	?>

	<div class="row">
		<div class="col-xs-12 text-center"><a class="common-btn" href="<?php echo SITE_URL; ?>mysupport.php">Purchase</a></div>
	</div>

	<script type="text/javascript">

	$("#hide_payment").click(function () {
		$("#payment_info").hide();
	});

	$("#show_payment").click(function () {
		$("#payment_info").show('fast');
	});

	</script>


<?php require_once ("inc/footer.inc.php"); ?>