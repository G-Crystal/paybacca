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


	if (isset($_POST["action"]) && $_POST["action"] == "message_reply")
	{
		unset($errors);
		$errors = array();

		$message_id	= (int)getPostParameter('messageid');
		$user_id	= (int)getPostParameter('uid');
		$answer		= mysql_real_escape_string(nl2br(getPostParameter('answer')));

		if (!($message_id && $user_id && $answer))
		{
			$errors[] = "Please enter your reply";
		}

		if (count($errors) == 0)
		{
			$ins_query = "INSERT INTO cashbackengine_messages_answers SET message_id='$message_id', user_id='$user_id', is_admin='1', answer='$answer', answer_date=NOW()";
			if (smart_mysql_query($ins_query))
			{
				smart_mysql_query("UPDATE cashbackengine_messages SET viewed='1', status='replied' WHERE message_id='$message_id'");
				header("Location: messages.php?msg=sent");
				exit();
			}
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$mid	= (int)$_GET['id'];
		$pn		= (int)$_GET['pn'];

		$query = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM cashbackengine_messages m, cashbackengine_users u WHERE m.user_id=u.user_id AND m.message_id='$mid'";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}

	$title = "Message Reply";
	require_once ("inc/header.inc.php");

?>   
    
	<?php

		if ($total > 0)
			{
				$row = mysql_fetch_array($result);
	?>

	   <h2>Message Reply</h2>


		<?php if (isset($errormsg)) { ?>
			<div style="width:550px;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

		<form action="" method="post" name="form1">
          <table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="5" cellspacing="5" border="0">
            <tr>
              <td width="40%" nowrap="nowrap" valign="middle" align="right" class="tb2">To:</td>
              <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
            </tr>
            <tr>
              <td nowrap="nowrap" valign="middle" align="right" class="tb2">Subject:</td>
              <td nowrap="nowrap" valign="top"><b><?php echo $row['subject']; ?></b></td>
            </tr>
           <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb2">Message:</td>
            <td height="70" style="border: 1px solid #EEE; background: #F9F9F9; border-radius: 5px;" align="left" valign="top"><?php echo $row['message']; ?></td>
          </tr>
           <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb2">Reply:</td>
            <td align="left" valign="top"><textarea cols="77" rows="5" name="answer" class="textbox2"><?php echo getPostParameter('answer'); ?></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="messageid" id="messageid" value="<?php echo (int)$row['message_id']; ?>" />
				<input type="hidden" name="uid" id="uid" value="<?php echo (int)$row['user_id']; ?>" />
				<input type="hidden" name="action" id="action" value="message_reply">
				<input type="submit" name="reply" id="reply" class="submit" value="Send" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='messages.php'" />
            </td>
          </tr>
          </table>
		</form>

      <?php }else{ ?>
			<p align="center">Sorry, no message found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>