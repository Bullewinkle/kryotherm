<?
    require_once($_SERVER['DOCUMENT_ROOT']."/settings.inc.php");       // ���������� ���������
    require_once($_SERVER['DOCUMENT_ROOT']."/classes/authorization.php");  // ���������� ������� �� ������ � ��

    $auth = new CAuthorization();       // ���������� ��� ������ � ��

    require_once(CATALOG_SCRIPT_DIR."catalog.php");
    require_once(CATALOG_SCRIPT_DIR."class_filter.php");

    session_start();
    unset($_REQUEST['PHPSESSID']);
    unset($_REQUEST['_ym_visorc']);

    $filter = new catalog_filter($auth, $idCat);
    $_SESSION['filter_data'] = $filter->electro_filter_values($_REQUEST);
    $_SESSION['filter_search'] = $_REQUEST;

    include(CATALOG_SCRIPT_DIR.'filter_html.php');
?>