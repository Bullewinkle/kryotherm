<?
  $bction=$HTTP_POST_VARS["bction"];
  session_start();


  extract($HTTP_GET_VARS);

  if (@is_array($_REQUEST)) foreach ($_REQUEST as $key => $value)
    if (stripos($value, "select")!==false) die("Thank you!");

  require_once("settings.inc.php");       // ���������� ���������
  require_once("classes/authorization.php");  // ���������� ������� �� ������ � ��
  require_once("functions/module.php");         // ��������� �������
  require_once("functions/menu.module.php");  // ������� ���������� ����
  //require_once("captcha.php");

  $auth = new CAuthorization();       // ���������� ��� ������ � ��

  session_start();

  if (file_exists(CATALOG_SCRIPT_DIR."catalog.php"))
      require_once(CATALOG_SCRIPT_DIR."catalog.php");

  if (file_exists(CATALOG_SCRIPT_DIR."class_filter.php"))
  {
      require_once(CATALOG_SCRIPT_DIR."class_filter.php");
      $filter = new catalog_filter($auth, $idCat);
  }

   mysql_query('SET NAMES cp1251');
   mysql_query('SET CHARACTER SET cp1251');
   mysql_query('SET COLLATION_CONNECTION=cp1251_general_ci');



  if ($bction=="send")
  {
    $name=$HTTP_POST_VARS["name"];
    $email=$HTTP_POST_VARS["email"];
    $mess=$HTTP_POST_VARS["comments"];


    if (empty($_REQUEST['code']) or ($_REQUEST['code'] !== $_SESSION['code'])) $badcode = 1; else $badcode = '';

    if(trim($name)==""||trim($email)=="")
    {
      echo("<script language=javascript>alert('�� �� ��������� ��� ������������ ����!');document.location.href='/order.php';</script>");
      exit();
    };

    if(!empty($badcode))
    {

      echo("<script language=javascript>alert('������� ������� ����� � �������� !');document.location.href='/order.php';</script>");
      exit();
    }

    if($name=="") $name = " - �� ������� - ";

    $date=date('d M Y, H:i:s');

    $mess=" ������ � ����� ".$_SERVER['SERVER_NAME']." �� ".$date." \n  \n ���: ".$name."  \n E-Mail: ".$email." \n \n �����������: \n".$mess;

    if($email=="���") $email = "anonymous@".$_SERVER['HTTP_HOST'];

    $from="FROM: ".$email." \nContent-Type: text/plain; charset=windows-1251\nContent-Transfer-Encoding: 8bit";

    $fostasmail=MAIL_INFO;
    mail($fostasmail,"������ � �����", $mess, $from) or  printf("<font color=red><b> �� </b></font>");

    print "<script language='javascript'>alert('������� ����������!');document.location.href='/order.php';</script>";

    exit;
  };

  $_SESSION['code'] = substr(md5(uniqid("")),0,4);
  require_once ("head.inc.php");
?>
<style type="text/css">
 .p10 {padding: 10px 0 0 0}
</style>
<div style="height: 54px;">
<a href="/">� �������</a> &gt; <span class="active">������ ������</span>
</div>
<h1>�������� �����:</h1>
<form action="<?echo "$PHP_SELF?bction=send";?>" method=post>
<table width="100%" border="0" cellpadding=10 cellspacing=5 class="order">
 <tr><td>�� ����:</td></tr>
  <tr><td><input type="text" name="name" maxlength="20" size="25" value=""></td></tr>
 <tr><td class="p10">���������� ����������:</td></tr>
  <tr><td><input type="text" name="email" maxlength="60" size="25" value=""></td></tr>
 <tr><td class="p10">����� ������ ��� �������:</td></tr>
  <tr><td><textarea name="comments" cols="50" rows="6"></textarea></td></tr>
 <tr><td class="p10">������� ����� � ��������:</td></tr>
  <tr><td><input type='text' id='code' name='code' size='4' maxlength='4' value="<?=$_REQUEST['code'];?>"><img src='captcha.php' align='absmiddle'></td></tr>
 <tr><td><br><input type=hidden name=bction value=send> <input type="submit" value="���������"></td></tr>
</table>

</form>

<? require_once ("foot.inc.php"); ?>