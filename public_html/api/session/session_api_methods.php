<?

function set_user_data() {
	$data = file_get_contents('php://input');
//	$data = json_encode($data);
//	$data = json_decode($data);

	if (!empty($data)) {
		$_SESSION['user_data'] = $data;
	}

	$result = $_SESSION['user_data'];
//	$result = json_encode($result);
	//$result = prettyPrint($result); // if you want to see formatted JSON - you can do so.

	return $result;
}

function get_user_data() {

	$result = $_SESSION['user_data'];

	$result = json_encode($result);
	//$result = prettyPrint($result); // if you want to see formatted JSON - you can do so.

	return $result;
}

function unset_user_data() {
	unset($_SESSION['user_data']);
}







function utf8_encode_deep(&$input) {
	if (is_string($input)) {
		$input = utf8_encode($input);
	} elseif (is_array($input)) {
		foreach ($input as &$value) {
			utf8_encode_deep($value);
		}

		unset($value);
	} elseif (is_object($input)) {
		$vars = array_keys(get_object_vars($input));

		foreach ($vars as $var) {
			utf8_encode_deep($input->$var);
		}
	}
}

?>