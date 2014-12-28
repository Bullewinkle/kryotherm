<?
  $bction=$HTTP_POST_VARS["bction"];
  session_start();


  extract($HTTP_GET_VARS);

  if (@is_array($_REQUEST)) foreach ($_REQUEST as $key => $value)
    if (stripos($value, "select")!==false) die("Thank you!");

  require_once("settings.inc.php");       // подключаем константы
  require_once("classes/authorization.php");  // подключаем функции по работе с БД
  require_once("functions/module.php");         // остальные функции
  require_once("functions/menu.module.php");  // функции построения меню
  //require_once("captcha.php");

  $auth = new CAuthorization();       // переменная для работы с БД

  session_start();

  if (file_exists(CATALOG_SCRIPT_DIR."catalog.php"))
      require_once(CATALOG_SCRIPT_DIR."catalog.php");

  if (file_exists(CATALOG_SCRIPT_DIR."class_filter.php"))
  {
      require_once(CATALOG_SCRIPT_DIR."class_filter.php");
      $filter = new catalog_filter($auth, $idCat);
  }

   mysql_query('SET NAMES utf8');
   mysql_query('SET CHARACTER SET utf8');
   mysql_query('SET COLLATION_CONNECTION=utf8_general_ci');



  if ($bction=="send")
  {
    $name=$HTTP_POST_VARS["name"];
    $email=$HTTP_POST_VARS["email"];
    $mess=$HTTP_POST_VARS["comments"];


    if (empty($_REQUEST['code']) or ($_REQUEST['code'] !== $_SESSION['code'])) $badcode = 1; else $badcode = '';

    if(trim($name)==""||trim($email)=="")
    {
      echo("<script language=javascript>alert('Вы не заполнили все обязательные поля!');document.location.href='/order.php';</script>");
      exit();
    };

    if(!empty($badcode))
    {

      echo("<script language=javascript>alert('Неверно введено число с картинки !');document.location.href='/order.php';</script>");
      exit();
    }

    if($name=="") $name = " - не указано - ";

    $date=date('d M Y, H:i:s');

    $mess=" Запрос с сайта ".$_SERVER['SERVER_NAME']." от ".$date." \n  \n ФИО: ".$name."  \n E-Mail: ".$email." \n \n Комментарии: \n".$mess;

    if($email=="нет") $email = "anonymous@".$_SERVER['HTTP_HOST'];

    $from="FROM: ".$email." \nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit";

    $fostasmail=MAIL_INFO;
    mail($fostasmail,"Запрос с сайта", $mess, $from) or  printf("<font color=red><b> НЕ </b></font>");

    print "<script language='javascript'>alert('Успешно отправлено!');document.location.href='/order.php';</script>";

    exit;
  };

  $_SESSION['code'] = substr(md5(uniqid("")),0,4);
  require_once ("head.inc.php");
?>
<style type="text/css">
 .p10 {padding: 10px 0 0 0}
</style>
<div style="height: 54px;">
<a href="/">В каталог</a> &gt; <span class="active">Задать вопрос</span>
</div>
<h1>Обратная связь:</h1>
<form action="<?echo "$PHP_SELF?bction=send";?>" method=post>
<table width="100%" border="0" cellpadding=10 cellspacing=5 class="order">
 <tr><td>От кого:</td></tr>
  <tr><td><input type="text" name="name" maxlength="20" size="25" value=""></td></tr>
 <tr><td class="p10">Контактная информация:</td></tr>
  <tr><td><input type="text" name="email" maxlength="60" size="25" value=""></td></tr>
 <tr><td class="p10">Текст заявки или вопроса:</td></tr>
  <tr><td><textarea name="comments" cols="50" rows="6"></textarea></td></tr>
 <tr><td class="p10">Введите число с картинки:</td></tr>
  <tr><td><input type='text' id='code' name='code' size='4' maxlength='4' value="<?=$_REQUEST['code'];?>"><img src='captcha.php' align='absmiddle'></td></tr>
 <tr><td><br><input type=hidden name=bction value=send> <input type="submit" value="Отправить"></td></tr>
</table>

</form>

<? require_once ("foot.inc.php"); ?>