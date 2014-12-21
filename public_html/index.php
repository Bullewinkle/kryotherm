<?
  @extract($HTTP_GET_VARS);

  if (@is_array($_REQUEST)) foreach ($_REQUEST as $key => $value)
    if (!is_array($value))
    if (stripos($value, "select")!==false) die("Thank you!");



  require_once("settings.inc.php");       // подключаем константы
  require_once("classes/authorization.php");  // подключаем функции по работе с БД
  require_once("functions/module.php");       // остальные функции
  require_once("functions/menu.module.php");  // функции построения меню
  require_once("classes/class.phpmailer.php");

  $auth = new CAuthorization();       // переменная для работы с БД
  $mail = new PHPMailer();

  session_start();

  if (file_exists(CATALOG_SCRIPT_DIR."catalog.php"))
      require_once(CATALOG_SCRIPT_DIR."catalog.php");

  if (file_exists(CATALOG_SCRIPT_DIR."class_filter.php"))
  {
      require_once(CATALOG_SCRIPT_DIR."class_filter.php");
      $filter = new catalog_filter($auth, $idCat);
  }

  if (function_exists("create_cart_session") && !empty($_POST['buy']))
      create_cart_session($_POST);

  if (function_exists("create_compare_session") && !empty($_POST['compare']))
      create_compare_session($_POST);

  if (strpos($_SERVER['REQUEST_URI'], 'session-api/')) {
    require_once("session_api/index.php");
    return false;
  }

  require_once("body.php");           // получаем страницу
  require_once("analyticstracking.php");
  require_once ("head.inc.php");

  require_once ("foot.inc.php");
?>