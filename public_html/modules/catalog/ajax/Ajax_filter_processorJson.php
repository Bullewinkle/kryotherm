<?php
    header('Content-type: text/html; charset=utf-8');
    
    ob_start();
    require_once($_SERVER['DOCUMENT_ROOT']."/settings.inc.php");       // подключаем константы
    require_once($_SERVER['DOCUMENT_ROOT']."/classes/authorization.php");  // подключаем функции по работе с БД

    $auth = new CAuthorization();       // переменная для работы с БД

    require_once(CATALOG_SCRIPT_DIR."catalog.php");
    require_once(CATALOG_SCRIPT_DIR."class_filter.php");

    session_start();
    unset($_REQUEST['PHPSESSID']);
    unset($_REQUEST['_ym_visorc']);

    $filter = new catalog_filter($auth, $idCat);
    $_SESSION['filter_data'] = $filter->electro_filter_values($_GET);
    $_SESSION['filter_search'] = $_GET;
    ob_clean();
    include(CATALOG_SCRIPT_DIR.'filter_html.php');
    $json = array(
        'response' => iconv('cp1251', 'utf-8', str_replace("\r", "", preg_replace('/\s{2,}/', ' ', ob_get_clean())))
    );

    print json_encode($json);
?>