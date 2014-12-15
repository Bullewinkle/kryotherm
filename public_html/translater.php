<?
  @extract($HTTP_GET_VARS);

  // если сгенерирован файлом .htaccess внутренний параметр request_url
  if (!empty($_SERVER['REQUEST_URI']))
  {
    require_once("settings.inc.php");       // подключаем константы
    require_once("classes/authorization.php");  // подключаем функции по работе с БД

    $auth = new CAuthorization();

    $sql = "SELECT id, url FROM ".TBL_PREF."pages WHERE url='".mysql_real_escape_string($_SERVER['REQUEST_URI'])."'";
    list($id, $url) = $auth->QueryExecute($sql, array(0, 1));

    if (!empty($id[0])) $page_id = $id[0];
    elseif (strpos($_SERVER['REQUEST_URI'], "page_id="))
      $page_id = substr($_SERVER['REQUEST_URI'],
                        strpos($_SERVER['REQUEST_URI'], "page_id="+8),
                        strlen($_SERVER['REQUEST_URI'])-strpos($_SERVER['REQUEST_URI'], "page_id=")-8);
    include("index.php");
  }
?>