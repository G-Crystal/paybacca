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


	if (isset($_POST['action']) && $_POST['action'] == "edit_news")
	{
		unset($errs);
		$errs = array();

		$news_id			= (int)getPostParameter('news_id');
		$news_title			= mysql_real_escape_string(getPostParameter('news_title'));
		$news_description	= mysql_real_escape_string($_POST['news_description']);
		$status				= mysql_real_escape_string(getPostParameter('status'));

		if(!($news_title && $news_description && $status))
		{
			$errs[] = "Please fill in all fields";
		}

		if (count($errs) == 0)
		{
			$sql = "UPDATE cashbackengine_news SET news_title='$news_title', news_description='$news_description', status='$status' WHERE news_id='$news_id' LIMIT 1";

			if (smart_mysql_query($sql))
			{
				header("Location: news.php?msg=updated");
				exit();
			}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$news_id = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_news WHERE news_id='$news_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit News";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit News</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Title:</td>
            <td valign="top"><input type="text" name="news_title" id="news_title" value="<?php echo $row['news_title']; ?>" size="78" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td valign="top"><textarea name="news_description" cols="80" rows="12" id="editor" class="textbox2"><?php echo stripslashes($row['news_description']); ?></textarea></td>
          </tr>
		  <script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
		  <script>
				CKEDITOR.replace( 'editor' );
		  </script>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="news_id" id="news_id" value="<?php echo (int)$row['news_id']; ?>" />
			<input type="hidden" name="action" id="action" value="edit_news" />
			<input type="submit" name="save" id="save" class="submit" value="Update" />
			<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='news.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no news found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>