<?
@extract($HTTP_GET_VARS);           // ��������� ����������
// ���������� ������� POST

//  $_SERVER['DOCUMENT_ROOT'] = "/home/u565477573/public_html/";

$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
define("USER", "u565477573_therm");
define("PSW", "1992rhbjnthv");
define("HOST_NAME", "127.0.0.1");
define("DB_NAME", "u565477573_kryo");

include($_SERVER['DOCUMENT_ROOT']."/settings/config.php");

define("F_TXT_B",     70);      // ���-�� ������ �� ���������� �����
define("F_TXT_A",     70);      // �-�� �����. ������ ����� ���������� �����
define("F_CNT",       1);       // �-�� ��������� ����������, �. �. �����.
// (������� �������, �.�. �� ������ ����� �.)
define("F_LIMIT",     10);      // �-�� ������ �� 1 ��������
define("F_PAGE",      3);       // �-�� �������, ����� �. ����� ��������� ...
define("F_DESC",      80);      // ����. ����� ������������� �������� ��������

//define("MAIL_INFO",     "step@kreazone.ru");
?>