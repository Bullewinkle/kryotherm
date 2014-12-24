<?
  session_start();
  
  require_once("../settings.inc.php");        // подключаем константы
  require_once("../classes/authorization.php");   // подключаем функции по работе с БД
  require_once("../functions/module.php");          // остальные функции

  require("classes/class_common.php");
  require("classes/class_loadModules.php");
  require_once("classes/pclzip.lib.php");
  require_once("classes/class_settings.php");

  $auth = new CAuthorization();           // переменная для работы с БД
  $auth->Authorizate();                   // вызов диалога регистрации

  $settings = new Settings($auth);
  $modules = new loadModules(MODULE_INSTALL_DIR);
  $common = new Common($auth);


?>
<html>
  <head>
    <title>Управление :: пользователь <?=$_SERVER['PHP_AUTH_USER'];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <meta http-equiv="Content-Language" content="ru">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link rel=stylesheet type="text/css" href="screen.css">
    <SCRIPT LANGUAGE="JavaScript" SRC="../scripts/scripts.js"></SCRIPT>
    <SCRIPT LANGUAGE="JavaScript" SRC="../scripts/libs/prototype.js"></SCRIPT>

  </head>
  <body>
    <table cellpadding=0 cellspacing=0 border=0 id="main">
      <tr><td valign=bottom id="head"><table width=100%><tr>
        <td align=center valign=bottom width=25%>
          <a href="/c0ntr0lz0ne/index.php"><p>CMS</p></a><br>Система управления сайтом
        </td>
        <td valign=bottom align=right width=75%>
          <div class="button1"><a href="/" onClick="document.execCommand('ClearAuthenticationCache');" title="Выйти из панели управления">Выход</a></div>
          <div class="button1"><a href="/" target="_blanck" title="Главная страница сайта в новом окне">Сайт</a></div>
        </td>
      </tr></table></td></tr>
      <tr id="cont">
        <td><div id="centr"><div id="headl"><div id="headr"><div id="footl"><div id="footr">
<? require_once("admin_head.php"); ?>
        </div></div></div></div></div></td>
      </tr>
      <tr><td align=right valign=bottom id="foot">
        <!--
        <a href="http://www.kreazone.ru" target="_blanck" title="Перейти на сайт разработчика">
          <img src="images/logo.jpg" border=0 width=85 height=20 alt="Step In Design">
        </a>
        -->
      </td></tr>
    </table>
  </body>
</html>