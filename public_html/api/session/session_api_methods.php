<?

function set_user_data() {
	$data = file_get_contents('php://input');
	$data = json_decode($data);

	if (!empty($data)) {
		$_SESSION['user_data'] = $data;
	}

	$result = $_SESSION['user_data'];
	$result = json_encode($result);
	//$result = prettyPrint($result); // if you want to see formatted JSON - you can do so.

	return $result;
}

function get_user_data() {

	if (!empty($fild)) {
		$result = $_SESSION['user_data'][$field];
	} else {
		$result = $_SESSION['user_data'];
	}

	$result = json_encode($result);
	//$result = prettyPrint($result); // if you want to see formatted JSON - you can do so.

	return $result;
}

function unset_user_data() {
	unset($_SESSION['user_data']);
}

?>