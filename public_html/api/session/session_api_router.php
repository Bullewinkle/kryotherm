<?

if (strpos($_SERVER['REQUEST_URI'], 'set_user_data')) {

	echo set_user_data();

}
if (strpos($_SERVER['REQUEST_URI'], 'get_user_data')) {

	echo get_user_data();

}

?>