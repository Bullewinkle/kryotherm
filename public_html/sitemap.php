<?
  extract($HTTP_GET_VARS);

  if (@is_array($_REQUEST)) foreach ($_REQUEST as $key => $value)
    if (stripos($value, "select")!==false) die("Thank you!");

  require_once("settings.inc.php");       // ���������� ���������
  require_once("classes/authorization.php");  // ���������� ������� �� ������ � ��
  require_once("functions/module.php");         // ��������� �������
  require_once("functions/menu.module.php");  // ������� ���������� ����

  $auth = new CAuthorization();       // ���������� ��� ������ � ��

require_once ("head.inc.php");
?>

<h1>����� �����</h1>

<?
  $map = TreeMenu($auth, 0, 1, 0);

  for($i=0; $i<count($map); $i++)
    if(isset($map[$i]["id"]))
      print "<p style='margin-left: ".$map[$i]["level"]*(INDENT+2)."px; margin-bottom: 10px;'><a href='".
              ((empty($map[$i]["url"]))?URL."index.php?page_id=".$map[$i]["id"]:$map[$i]["url"])."'><b>".$map[$i]["name"]."</b></a> / ".$map[$i]["descr"]."</p>";

require_once ("foot.inc.php");
?>