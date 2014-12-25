<?

class Response {
	public $status = 0;
	public $data = null;
}

function set_user_data() {
	$data = file_get_contents('php://input');
	$data = json_decode($data);

	if (!empty($data)) {
		$_SESSION['user_data'] = $data;
	}

	return get_user_data();
}

function get_user_data() {

	$result = new Response();
	$result -> data = $_SESSION['user_data'];
	$result = json_encode($result);
	//$result = prettyPrint($result); // if you want to see formatted JSON - you can do so.

	return $result;
}

function unset_user_data() {
	unset($_SESSION['user_data']);
}


?>