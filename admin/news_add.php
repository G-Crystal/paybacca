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


	if (isset($_POST['action']) && $_POST['action'] == "add_news")
	{
		unset($errs);
		$errs = array();

		$news_title			= mysql_real_escape_string(getPostParameter('news_title'));
		$news_description	= mysql_real_escape_string($_POST['news_description']);

		if(!($news_title && $news_description))
		{
			$errs[] = "Please fill in all fields";
		}

		if (count($errs) == 0)
		{
			$sql = "INSERT INTO cashbackengine_news SET news_title='$news_title', news_description='$news_description', status='active', added=NOW()";

			if (smart_mysql_query($sql))
			{
				header("Location: news.php?msg=added");
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

	$title = "Add News";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Add News</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div style="width:60%;" class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Title:</td>
            <td valign="top"><input type="text" name="news_title" id="news_title" value="<?php echo getPostParameter('news_title'); ?>" size="78" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td valign="top"><textarea name="news_description" cols="75" rows="12" id="editor" class="textbox2"><?php echo stripslashes($_POST['news_description']); ?></textarea></td>
          </tr>
		  <script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
		  <script>
				CKEDITOR.replace( 'editor' );
		  </script>
          <tr>
            <td colspan="2" align="center" valign="middle">
				<input type="hidden" name="action" id="action" value="add_news" />
				<input type="submit" name="add" id="add" class="submit" value="Add News" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='news.php'" />
		  </td>
          </tr>
        </table>
      </form>


<?php require_once ("inc/footer.inc.php"); ?>