<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	// Error Reporting
	//@error_reporting(0);

	/// MySQL Settings ///
	define('DB_NAME', 'pareciap_pcw');	// MySQL database name
	define('DB_USER', 'pareciap_pcw');			// MySQL database user
	define('DB_PASSWORD', 'Vault111!');		// MySQL database password
	define('DB_HOST', 'localhost');				// MySQL database host name (in most cases, it's localhost)


	define("CashbackEngine", true);
	define('PUBLIC_HTML_PATH', $_SERVER['DOCUMENT_ROOT']);
	define('IMAGES_PATH', $_SERVER['DOCUMENT_ROOT']."/img/");
	define('DOCS_ROOT', $_SERVER['DOCUMENT_ROOT']);
	define('CBengine_ROOT', dirname(__FILE__) . '/');
	define('CBengine_PAGE', true);

	require_once(CBengine_ROOT."db.inc.php");
	require_once(CBengine_ROOT."functions.inc.php");

	if (!defined('is_Setup'))
	{
		require_once(CBengine_ROOT."siteconfig.inc.php");
		require_once(CBengine_ROOT."timezone.inc.php");

		// setup time zone
		if (in_array(SITE_TIMEZONE, $timezone))
		{
			date_default_timezone_set(SITE_TIMEZONE);
		}

		$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'english';

		if (MULTILINGUAL !=0 && !empty($lang) && file_exists(DOCS_ROOT."/language/".$lang.".inc.php"))
		{
			define('USER_LANGUAGE', $lang);
			require_once(DOCS_ROOT."/language/".$lang.".inc.php");
		}
		else
		{
			define('USER_LANGUAGE', SITE_LANGUAGE);
			require_once(DOCS_ROOT."/language/".SITE_LANGUAGE.".inc.php");
		}
	}

	// maintenance mode //
	if (SITE_MODE == 'maintenance' && !$admin_panel)
	{
		require_once(DOCS_ROOT."/maintenance.php");
		die();
	}

	// delete redirection url after 10 minutes
	if (isset($_SESSION['goto']) && $_SESSION['goto'] != "" && isset($_SESSION['goto_created']) && (time() - $_SESSION['goto_created'] > 600))
	{
		unset($_SESSION['goto'], $_SESSION['goto_created'], $_SESSION['goRetailerID'], $_SESSION['goCouponID']);
	}

?>