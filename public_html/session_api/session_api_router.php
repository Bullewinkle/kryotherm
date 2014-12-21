<?
/**
 * Created by IntelliJ IDEA.
 * User: dmitriygalnykin
 * Date: 21.12.14
 * Time: 18:21
 */


if (strpos($_SERVER['REQUEST_URI'], 'set_user_data')) {

	echo set_user_data($_POST);

}
if (strpos($_SERVER['REQUEST_URI'], 'get_user_data')) {

	echo get_user_data();

}
return false;
?>