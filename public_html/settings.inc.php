<?
@extract($HTTP_GET_VARS);           // получение переменных
// переданных методом POST

//  $_SERVER['DOCUMENT_ROOT'] = "/home/u565477573/public_html/";

$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
define("USER", "u565477573_therm");
define("PSW", "1992rhbjnthv");
define("HOST_NAME", "127.0.0.1");
define("DB_NAME", "u565477573_kryo");

include($_SERVER['DOCUMENT_ROOT']."/settings/config.php");

define("F_TXT_B",     70);      // кол-во текста до найденного слова
define("F_TXT_A",     70);      // к-во отобр. текста после найденного слова
define("F_CNT",       1);       // к-во найденных совпадений, к. б. отобр.
// (верхняя граница, т.е. не больше этого ч.)
define("F_LIMIT",     10);      // к-во ссылок на 1 странице
define("F_PAGE",      3);       // к-во страниц, после к. будет ставиться ...
define("F_DESC",      80);      // макс. длина отображаемого описания страницы

//define("MAIL_INFO",     "step@kreazone.ru");
?>