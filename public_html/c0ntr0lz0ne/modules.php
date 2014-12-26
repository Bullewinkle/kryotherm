<?
$auth->Authorizate();

    if($_REQUEST['action'] == 'add_modules')
    {
        if(!empty($_FILES['module']['tmp_name']))
        {
            if(!file_exists(MODULE_INSTALL_DIR.$_FILES['module']['name']))
            {
                if(strrchr($_FILES['module']['name'],'.') == MODULE_FILE_TYPE)

                    if(move_uploaded_file($_FILES['module']['tmp_name'],MODULE_INSTALL_DIR.$_FILES['module']['name']))
                    {
                        $modules->zip = new PclZip(MODULE_INSTALL_DIR.$_FILES['module']['name']);
                        $modules->installModule();
                        $modules->mesage = "<span class='green'>Успешно</span>";
                    }

            }else $modules->mesage = "<span class='red'>Такой модуль уже установлен</span>";
        }

        $settings->createConfig();              // обновляем файл конфигурации
        $common->redirect("/c0ntr0lz0ne/index.php?action=modules",'1000');

    }
    elseif($_REQUEST['action'] == 'del_modules')
    {
        $modules->zip = new PclZip(MODULE_INSTALL_DIR.$_REQUEST['module']);
        $modules->arch = $_REQUEST['module'];
        $modules->deleteModule();
        $settings->createConfig();              // обновляем файл конфигурации
        $common->redirect("/c0ntr0lz0ne/index.php?action=modules",'1000');
    }
?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width=150><img src="images/page-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top width="100%">
                      <h1>Добавление модулей</h1>
                      <form name="FormName" enctype="multipart/form-data" action="index.php?action=add_modules" method="post">
                               <input name="module" type="file" value="">
                               <input type="submit" value="Установить">
                      </form>
                      <p><?=$modules->mesage;?></p>
                    </td>
                  </tr>
                </table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody><tr>
                    <td valign="top" style="padding-right: 20px;">
                      <div style="background: transparent url(images/points.gif) repeat-x scroll center top; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: 100%; margin-top: 10px;">&nbsp;</div>
                      <div id="dataTable"><?=$modules->getModulesList();?></div>
                    </td></tr>
                </table>