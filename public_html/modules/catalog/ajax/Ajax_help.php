<?php
    header('Content-type: text/html; charset=utf-8');

    require_once($_SERVER['DOCUMENT_ROOT']."/settings.inc.php");       // ���������� ���������
    require_once($_SERVER['DOCUMENT_ROOT']."/classes/authorization.php");  // ���������� ������� �� ������ � ��

    $auth = new CAuthorization();       // ���������� ��� ������ � ��

    $sql = "SELECT * FROM ".TBL_PREF."pages WHERE id = '".$_REQUEST['page_id']."'";
    $q = mysql_query($sql);
    $r = mysql_fetch_array($q);

    print iconv("windows-1251", "UTF-8", $r['story_text']);
?>