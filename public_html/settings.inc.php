<?
  @extract($HTTP_GET_VARS);           // ��������� ����������
                                     // ���������� ������� POST

//  $_SERVER['DOCUMENT_ROOT'] = "//";

  $_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
  define("USER", "kryother_nshop");
  define("PSW", "gjLg83lJ");
  define("HOST_NAME", "localhost");
  define("DB_NAME", "kryother_nshop");

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