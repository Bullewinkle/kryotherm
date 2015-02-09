<?php
// set error reporting levels to prevent errors in PHP 5
if (defined('E_STRICT')) {
	if (!isset($WP_PRE_ERROR_LEVEL)) {
		$WP_PRE_ERROR_LEVEL = ini_get('error_reporting');
		if ($WP_PRE_ERROR_LEVEL == E_STRICT) {
			error_reporting(E_ALL);
		}
	}
}
?>