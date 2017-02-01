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

	$where = "";

	if (isset($_GET['filter']) && $_GET['filter'] != "")
	{
		$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
		$where .= " AND (reference_id='$filter' OR payment_type LIKE '%$filter%')";
	}

	if (isset($_GET['date']) && $_GET['date'] != "")
	{
		$date	= mysql_real_escape_string(getGetParameter('date'));
		$where .= " AND DATE(created)='$date'";
	}

	if (isset($_GET['start_date']) && $_GET['start_date'] != "")
	{
		$start_date	= mysql_real_escape_string(getGetParameter('start_date'));
		$where .= " AND created>='$start_date 00:00:00'";
	}

	if (isset($_GET['end_date']) && $_GET['end_date'] != "")
	{
		$end_date = mysql_real_escape_string(getGetParameter('end_date'));
		$where .= " AND created<='$end_date 23:59:59'";
	}


	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS payment_date FROM cashbackengine_transactions WHERE 1=1 ".$where." ORDER BY created DESC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{		
		$filename_add = "";

		if ($date) $filename_add .= "_".$date;

		if ($filename_add == "")
			$filename = "payments_".time().".xls";
		else
			$filename = "payments".$filename_add.".xls";


        $contents = "Reference ID \t Username \t Payment Type \t Amount \t Date \t Status \t \n";

		while ($row = mysql_fetch_array($result))
		{
			$contents .= html_entity_decode($row['transaction_id'], ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= html_entity_decode(GetUsername($row['user_id']), ENT_NOQUOTES, 'UTF-8')."\t";

			switch ($row['payment_type'])
			{
				case "Cashback":			$payment_type = PAYMENT_TYPE_CASHBACK; break;
				case "Withdrawal":			$payment_type = PAYMENT_TYPE_WITHDRAWAL; break;
				case "Referral Commission": $payment_type = PAYMENT_TYPE_RCOMMISSION; break;
				case "friend_bonus":		$payment_type = PAYMENT_TYPE_FBONUS; break;
				case "signup_bonus":		$payment_type = PAYMENT_TYPE_SBONUS; break;
				default:					$payment_type = $row['payment_type']; break;
			}

			$contents .= html_entity_decode($payment_type, ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= DisplayMoney($row['amount'], $hide_currency = 1)."\t";
			$contents .= $row['payment_date']."\t";
			$contents .= $row['status']."\t";
			$contents .= " \n"; 
        }

		header('Content-type: application/ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);

		echo $contents;
		exit;
	}

?>