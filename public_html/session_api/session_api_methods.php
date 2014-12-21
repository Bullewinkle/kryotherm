<?

// EXAMPLE

//function create_cart_session($data) {
//	//session_name("user_cart");
//
//	if (!is_array($_SESSION['user_cart']))
//		$_SESSION['user_cart'] = array();
//
//	if (!empty($data['cart_prod_quant']) &&
//		!empty($data['cart_prod_id'])
//	) {
//		if ($data['cart_prod_quant'] > $data['cart_prod_total']) {
//			print '<script text="Javascript"> alert("В наличии только ' . $data['cart_prod_total'] . ' шт."); document.location.href="/index.php?idCat=' . $data['cart_cat_id'] . '" </script>';
//		} else {
//			if (empty($_SESSION['user_cart'][$data['cart_prod_id']]))
//
//				$_SESSION['user_cart'] = $_SESSION['user_cart'] + array($data['cart_prod_id'] => $data);
//
//			if (!empty($_SESSION['user_cart'][$data['cart_prod_id']]))
//
//				$_SESSION['user_cart'][$data['cart_prod_id']]['cart_prod_quant'] = $data['cart_prod_quant'];
//		}
//	} elseif (empty($data['cart_prod_quant']) &&
//		!empty($data['cart_prod_id']) &&
//		!empty($_SESSION['user_cart'][$data['cart_prod_id']]['cart_prod_quant'])
//	)
//
//		unset($_SESSION['user_cart'][$data['cart_prod_id']]);
//}

function set_user_data() {
	$data = file_get_contents('php://input');
	$data = json_decode($data);

	if (!empty($data)) {
		$_SESSION['user_data'] = $data;
	} else {
		unset($_SESSION['user_data']);
	}

	$result = $_SESSION['user_data'];
	$result = json_encode($result);
	$result = prettyPrint($result);
	return $result;
}

function get_user_data() {

	if (!empty($fild)) {
		$result = $_SESSION['user_data'][$field];
	} else {
		$result = $_SESSION['user_data'];
	}

	$result = json_encode($result);
	$result = prettyPrint($result);
	return $result;
}

?>