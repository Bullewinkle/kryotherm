<?php
    header('Content-type: text/html; charset=utf-8');

    require_once($_SERVER['DOCUMENT_ROOT']."/settings.inc.php");       // подключаем константы
    require_once($_SERVER['DOCUMENT_ROOT']."/classes/authorization.php");  // подключаем функции по работе с БД

    $auth = new CAuthorization();       // переменная для работы с БД

    $sql = "SELECT * FROM ".TBL_PREF."pages WHERE id = '".$_REQUEST['page_id']."'";
    $q = mysql_query($sql);
    $r = mysql_fetch_array($q);

    print $r['story_text'];
?>