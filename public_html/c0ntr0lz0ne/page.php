<?
$auth->Authorizate();
?>
<table width="100%">
<?
  if(!empty($_REQUEST['action'])) // если выбрали действие
  {
    // если установили номер страницы, то присвоим его обычной переменной
    $page_id = isset($_REQUEST['page_id'])?$_REQUEST['page_id']:0;
    $cPath = isset($_REQUEST['cPath'])?$_REQUEST['cPath']:0;

    switch($_REQUEST['action'])   // определяем это действие
    {
      case "del_page":            // удалить страницу
        if(!empty($page_id))      // если установили идентификатор страницы, то
        {                         // проверяем - есть ли у страницы потомки
          $sql = "select p2.id, p1.image from ".TBL_PREF."pages p1
                  left join ".TBL_PREF."pages p2 on p1.id=p2.id_parent
                  where p1.id=".$page_id;
          list($id, $image) = $auth->QueryExecute($sql, array(0,1));

          $id = $id[0];
          if (empty($id))         // если потомков нет - удаляем страницу
          {
             if(file_exists(A_PAGE_ICON_DIR.$image[0])) unlink(A_PAGE_ICON_DIR.$image[0]);
            // после удаления связанных изображений и документов удаляем стр.
            $auth->QueryExecute("delete from ".TBL_PREF."pages where id=$page_id");
          }
        }

      case "insert_page":         // добавление страницы
        if (!empty($_REQUEST['name']) and isset($_REQUEST['level']) and isset($_REQUEST['parent']))
        {
          // проверяем страницу на существование
          $page_id = $auth->GetPageId(addslashes(stripslashes($_REQUEST['name'])), $_REQUEST['parent']);

          if(empty($page_id))     // если такой страницы нет то заносим новую
          {                       // определяем максимальный индекс сортировки
            $sql = "select max(sort_index) from ".TBL_PREF."pages where id_parent=".$_REQUEST['parent'];
            list($sort_index) = $auth->QueryExecute($sql, 0);

            $sql = "insert into
                    ".TBL_PREF."pages(id, id_parent, id_user, name, headline,
                                      story_text, created, modified, published,
                                      keywords, description, level, url,
                                      sort_index, title)
                    values('', ".$_REQUEST['parent'].", 1,
                           '".addslashes(stripslashes($_REQUEST['name']))."',
                           '".addslashes(stripslashes($_REQUEST['headl']))."',
                           '".addslashes(stripslashes($_REQUEST['text']))."',
                           '".date('Y:m:d')."', '".date('Y:m:d')."', 1,
                           '".addslashes(stripslashes($_REQUEST['keyw']))."',
                           '".addslashes(stripslashes($_REQUEST['descr']))."', ".
                           ($_REQUEST['level']+1).",
                           '".addslashes(stripslashes($_REQUEST['url']))."', ".
                           ((empty($sort_index))?1:++$sort_index).",
                           '".addslashes(stripslashes($_REQUEST['title']))."'".")";
            $auth->QueryExecute($sql);

            $page_id = $auth->GetPageId(addslashes(stripslashes($_REQUEST['name'])),
                                        $_REQUEST['parent']);
          }

          $page_id = $page_id[0]; // приводим идентификатор к нормалному виду
        }

      case "update_page":         // обновление страницы
        if (($_REQUEST['action']=="update_page") and !empty($page_id))
          if(!empty($_REQUEST['name']))
          {
            $sql = "update ".TBL_PREF."pages set
                      name='".addslashes(stripslashes($_REQUEST['name']))."'".
                   ", headline='".addslashes(stripslashes($_REQUEST['headl']))."'".
                   ", story_text='".addslashes(stripslashes($_REQUEST['text']))."'
                    , modified='".date('Y:m:d')."'".
                   ", keywords='".addslashes(stripslashes($_REQUEST['keyw']))."'".
                   ", description='".addslashes(stripslashes($_REQUEST['descr']))."'".
                   ", url='".addslashes(stripslashes($_REQUEST['url']))."'".
                   ", title='".addslashes(stripslashes($_REQUEST['title']))."'
                   ".(!empty($_REQUEST['img_del'])?', image=""':'')."
                   where id=".$page_id;
            $auth->QueryExecute($sql);

            if(file_exists(A_PAGE_ICON_DIR.$_REQUEST['img_del']) and !empty($_REQUEST['img_del'])) unlink(A_PAGE_ICON_DIR.$_REQUEST['img_del']);
          }

          if(!empty($_FILES['userfile']['tmp_name']))
          {
            $qid = $page_id;

               switch($_FILES['userfile']['type']){
                      case "image/gif":
                      case "image/jpg":
                      case "image/jpeg":
                      case "image/pjpeg":

                            $filename = A_PAGE_ICON_DIR.$qid.$_FILES['userfile']['type'];
                            $filename = str_replace("image/", ".", $filename);
                            $filename = str_replace("pjpeg", "jpg", $filename);
                            $filename = str_replace("jpeg", "jpg", $filename);

                            if(move_uploaded_file($_FILES['userfile']['tmp_name'], $filename)){

                               $filename = str_replace(A_PAGE_ICON_DIR, "", $filename);
                               $sql = mysql_query("update ".TBL_PREF."pages set image='$filename' where id ='$qid'");
                               }

                      break;

                      default: echo "<br>Неверный формат файла: ".$_FILES['userfile']['type'];
                      }
          }

      case "lock_page":           // запретить отображение страницы
          if(!empty($page_id) and ($_REQUEST['action']=="lock_page"))
            $auth->QueryExecute("update ".TBL_PREF."pages set published=0 where id=$page_id");

      case "unlock_page":         // разрешить отображение страницы
          if(!empty($page_id) and ($_REQUEST['action']=="unlock_page"))
            $auth->QueryExecute("update ".TBL_PREF."pages set published=1 where id=".$page_id);

      case "up_page":           // поднять страницу
          if(!empty($page_id) and ($_REQUEST['action']=="up_page"))
            PageMove($auth, $page_id, "up");

      case "down_page":         // опустить страницу
          if(!empty($page_id) and ($_REQUEST['action']=="down_page"))
            PageMove($auth, $page_id, "down");

      case "move_page":
          if(!empty($page_id) and ($_REQUEST['action']=="move_page"))
          {
            $sql = "SELECT id, level FROM ".TBL_PREF."pages WHERE id=".$page_id;
            list($id, $level) = $auth->QueryExecute($sql, array(0, 1));

            if (!empty($id[0]) and ($id[0]==$page_id))
            {
              $auth->QueryExecute("UPDATE ".TBL_PREF."pages SET id_parent=".$cPath."
                                   WHERE id=".$page_id);

              $sql = "SELECT id, level+2 FROM ".TBL_PREF."pages WHERE id=".$cPath;
              list($id_par, $level_par) = $auth->QueryExecute($sql, array(0, 1));

              if (empty($id_par[0])) $level_par[0] = 0;

              if ($level_par[0] != $level[0])
              {
                $auth->QueryExecute("UPDATE ".TBL_PREF."pages
                                     SET level=level".(($level_par[0]-$level[0])>0?'+':'').($level_par[0]-$level[0])."
                                     WHERE id=".$page_id);

                $sort_id = $sort_name = array();
                $pages_array = structure($auth);
                sort_structure($pages_array, $page_id);

                if (count($sort_id) > 0)
                  $auth->QueryExecute("UPDATE ".TBL_PREF."pages
                                       SET level=level".(($level_par[0]-$level[0])>0?'+':'').($level_par[0]-$level[0])."
                                       WHERE id in (".implode(",", $sort_id).")");
              }
            }
          }

      case "pages":               // выводим список всех активных страниц дерева
          if (empty($_REQUEST['apply']))
          {
                $sort_id = $sort_name = array();
                $pages_array = structure($auth);
                sort_structure($pages_array, 0);
?>
      <tr>
        <td colspan=7 valign=top>

                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width=150><img src="images/page-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top width="100%">
                      <h1>Управление страницами</h1>
<!--                      <table border=0 width=100% cellspacing=0 cellpadding=0> -->
<!--
                        <tr>
                          <td class="smallText" align="right">
                            <form action="page.php" method="get">
                              <input type=hidden name="selected_box" value="page">
                              <input type=hidden name="action" value="pages">
                              Поиск:
                              <input name="search" value="<?=(!empty($_REQUEST['search'])?$_REQUEST['search']:"");?>">
                            </form>
                          </td>
                        </tr>
-->
<!--                        <tr>
                          <td align="left"> -->
                            <form action="" method="get">
                              <input type=hidden name="selected_box" value="page">
                              <input type=hidden name="action" value="pages">
                              Перейти в:
                              <select name="cPath" onChange="this.form.submit();">
                                <option value=0>Начало</option>
                                <?=CreateSelOption($sort_id, $sort_name, !empty($cPath)?$cPath:0);?>
                              </select>
                            </form>
<!--                          </td>
                        </tr>
                      </table> -->
                    </td>
                  </tr>
                </table>

                <table border="0" width="100%" cellspacing="0" cellpadding="0" >
                  <tr>
                    <td valign="top" style="padding-right:20px;">
                      <div style="background:url(images/points.gif) top repeat-x; width:100%; margin-top:10px;">&nbsp;</div>
                      <?     SiteMap($auth, 4); ?>
                    </td>
                  </tr>
                </table>
        </td>
      </tr>
     <form name=fTree method=post>
      <tr>
        <td align=right width=30>
        </td>
        <td align=right colspan=6 style="padding-right:20px;"><br />
<?  if (!empty($_REQUEST['cPath'])) {  $back = get_parent($_REQUEST['cPath']); ?>

          <a href="?selected_box=page&action=pages&cPath=<?=$back;?>"><img src="images/icons/button-back.gif" border="0" /></a>
<?  } ?>
          <a href="?action=add_page&page_id=<?=!empty($cPath)?$cPath:0;?>"><img src="images/icons/add.gif" border=0></a>
        <br /><br />
        </td>
      </tr>
     </form>
<?
          break;
        }

      case "edit_page":           // редактирование страницы
        if(!empty($page_id))      // возможно в том случае,
        {                         // если есть что редактировать
?>
      <tr>
        <td colspan=7 valign=top style="padding-right:20px;">

                <table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
                  <tr>
                    <td width=150><img src="images/page-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top>

      <h1>Изменение страницы</h1>
<?                                // получение информации о странице
          $sql = "select * from ".TBL_PREF."pages where id=".$page_id;
          list($id, $parent, $name, $headl, $text, $keyw, $descr, $lev, $url, $p_title, $image) =
            $auth->QueryExecute($sql, array(0, 1, 3, 4, 5, 9, 10, 14, 15, 17, 22));

          $name[0]       = ((!empty($name[0]))?stripslashes($name[0]):"");
          $headl[0]      = ((!empty($headl[0]))?stripslashes($headl[0]):"");
          $text[0]       = ((!empty($text[0]))?stripslashes($text[0]):"");
          $keyw[0]       = ((!empty($keyw[0]))?stripslashes($keyw[0]):"");
          $descr[0]      = ((!empty($descr[0]))?stripslashes($descr[0]):"");
          $url[0]        = ((!empty($url[0]))?stripslashes($url[0]):"");
          $p_title[0]    = ((!empty($p_title[0]))?stripslashes($p_title[0]):"");
?>
        <div class="button2"><a href="#" onClick="fPage.apply.value='1'; fPage.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=pages&cPath=<?=((!empty($parent[0]))?$parent[0]:0);?>" title="">Отменить</a></div>
        <div class="button2"><a href="#" onClick="fPage.submit();" title="">Сохранить</a></div>

    <form name=fPage enctype="multipart/form-data"
          action="?action=update_page" method=post style="display:inline;">
      <input name=parent type=hidden value=<?=((isset($parent[0]))?$parent[0]:0);?>>
<?                                // получение списка связанных с ней файлов
        }

      case "add_page":            // создание страниц
        if(($_REQUEST['action']=="add_page") or empty($page_id))
        {
?>
      <tr>
        <td colspan=7 valign=top style="padding-right:20px;">

                <table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
                  <tr>
                    <td width=150><img src="images/page-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top>
        <h1>Добавление страницы в раздел:</h1>
<?
          $parent[0] = $page_id;
          $path = TreeMenu($auth, $parent[0], 2, 0);

          if(!empty($page_id))    // если добавляем страницу в существующий
          {                       // раздел то выводим путь к этому разделу
            for($i=(count($path)-1); $i>=0; $i--)
              if(!empty($path[$i]["name"]))
              {
                print $path[$i]["name"]."/";
                $lev[0] = $path[$i]["level"]+1;
              }
          }
          else                    // иначе пишем "Основной раздел"
          {                       // поэтому страница будет  находится
            $lev[0] = -1;         // на нулевом уровне
            print "Основной раздел";
          }
?>
        <div class="button2"><a href="#" onClick="fPage.apply.value='1'; fPage.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=pages&cPath=<?=((!empty($parent[0]))?$parent[0]:0);?>" title="">Отменить</a></div>
        <div class="button2"><a href="#" onClick="fPage.submit();" title="">Сохранить</a></div>

    <form name=fPage enctype="multipart/form-data"
          action="?action=insert_page" method=post style="display:inline;">
      <input name=parent type=hidden value=<?=((isset($page_id))?$page_id:0);?>>
<?      } ?>
      <input type=hidden name=apply value="">
      <input name=level  type=hidden value=<?=((isset($lev[0]))?$lev[0]:0);?>>
      <tr><td colspan=3 height=5><div style="height:5px; line-height:5px; background:url(images/points.gif) top repeat-x; width:100%;">&nbsp;</div></td></tr>
      <tr id="dataTable"><th colspan=3>&nbsp;</th></tr>
      <tr><td colspan=3>&nbsp;</td></tr>
      <tr>
        <td style="width: 12%">Текст меню</td>
        <td colspan=2>
          <input name=name type=text maxlength=100 style="width: 100%;"
                 value="<?=((isset($name[0]))?$name[0]:'Название');?>">
        </td>
      </tr>
      <tr>
        <td>Путь к источнику</td>
        <td colspan=2>
          <input name=url type=text maxlength=255 style="width: 100%;"
                 value="<?=((isset($url[0]))?$url[0]:'');?>">
        </td>
      </tr>
      <tr>
        <td>Заголовок страницы (в тексте)</td>
        <td colspan=2>
          <textarea name=headl style="width: 100%;" rows=1><?=((isset($headl[0]))?$headl[0]:'Заголовок');?></textarea>
        </td>
      </tr>
<script language="javascript">
<!--//
var currentTextArea = null
function openEditor(textarea) {

        // location of edit.php file:
        var editFile = '/c0ntr0lz0ne/editor_files/edit.php';

        currentTextArea = textarea;

        var edit = window.open(editFile, 'editorWindow', 'width=720, height=450');
        edit.focus();
}
//-->
</script>
      <tr>
        <td></td>
        <td colspan="2" style="padding-bottom:0" valign=middle>
          <a href="javascript:openEditor(document.fPage.text)" title="Визуальный редактор"><img src="images/icons/redaktor.gif" align=middle width="27" height="27" alt="Визуальный редактор"></a>
          <a href="javascript:openEditor(document.fPage.text)" title="Визуальный редактор" style="font-size:12px; vertical-align:middle;">Визуальный редактор</a>
        </td>
      </tr>
      <tr>
        <td>Текст</td>
        <td colspan=2>
            <textarea name=text style="width:100%;" rows=20><?=((isset($text[0]))?$text[0]:'Текст');?></textarea>
        </td>
      </tr>
      <!--
      <tr>
        <td>Картинка:</td>
        <td colspan=2>
        <?
          if(empty($image[0])){?>

            <input type="hidden" name="MAX_FILE_SIZE" value="<?=A_PAGE_ICON_SIZE;?>">
            <input type="file" name="userfile" SIZE=20><br>
            Файл jpg или gif. Не более 500 Кб.

          <? }else{?>

           <br>
           <img border=0 src="<?=A_PAGE_ICON_URL.$image[0]?>" vspace="4" hspace="10"><br>
           Удалить картинку: <input type="checkbox" name="img_del" value="<?=$image[0];?>">
        <? } ?>
        </td>
      </tr>
      -->
<tr bgcolor="#c0c0c0"><td colspan="3"><img src="../images/blank.gif" width=1 height=2 border=0></td></tr>
      <tr>
        <td vailgn=top>Заголовок страницы (title)</td>
        <td colspan=2>
          <textarea style="width: 100%;" rows=2 name=title><?=((!empty($p_title[0]))?$p_title[0]:DEF_TITLE);?></textarea>
        </td>
      </tr>
      <tr>
        <td vailgn=top>Ключевые слова (keywords)</td>
        <td colspan=2>
          <textarea style="width: 100%;" rows=2 name=keyw><?=((!empty($keyw[0]))?$keyw[0]:DEF_KEYW);?></textarea>
        </td>
      </tr>
      <tr>
        <td vailgn=top>Описание (description)</td>
        <td colspan=2>
          <textarea style="width: 100%;" rows=2 name=descr><?=((!empty($descr[0]))?$descr[0]:DEF_DESC);?></textarea>
        </td>
      </tr>
<tr bgcolor="#c0c0c0"><td colspan="3"><img src="../images/blank.gif" width=1 height=1 border=0></td></tr>
      <input name=page_id type=hidden value=<?=((!empty($page_id))?$page_id:0);?>>
      <tr>
        <td colspan=3 align=center>
          <input type=hidden name="cPath" value="<?=((!empty($parent[0]))?$parent[0]:0);?>">
        </td>
      </tr>
      <tr><td></td><td colspan=2>
        <div class="button2"><a href="#" onClick="fPage.apply.value='1'; fPage.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=pages&cPath=<?=((!empty($parent[0]))?$parent[0]:0);?>" title="">Отменить</a></div>
        <div class="button2"><a href="#" onClick="fPage.submit();" title="">Сохранить</a></div>
      </td></tr>
      <tr><td colspan=3 height=20><div style="height:20px; line-height:20px; background:url(images/points.gif) 0px 10px repeat-x; width:100%;">&nbsp;</div></td></tr>
    </form>
                    </td>
                  </tr>
                </table>
        </td>
      </tr>
<?
          break;
    }
  }
?>
    </table>