<?
// функция определяет конечный узел , до которого строится дерево;

  function definerEndLimit($start,$mode="")
  {
  $sql1 = mysql_query("SELECT id FROM ".TBL_PREF."pages WHERE  id_parent='0' ");
  while($result = mysql_fetch_array($sql1)) $string_id .= ','.$result['id'];

  if(empty($_REQUEST['page_id'])){

        $sql = mysql_query("SELECT id FROM ".TBL_PREF."pages WHERE  url='".$_SERVER['REQUEST_URI']."' AND id NOT IN (".substr($string_id, 1).") ");
        $result = mysql_fetch_array($sql);
        $param = $result['id'];

  }

   return $param;
  }

/*  Вырезает часть массива в начале, подготавливая его тем самым к постраничному выводу
*/
    function prepare_to_perpage($array, $limit, $perpage)
    {
        if ($limit*$perpage >= 0);
        {
            if (is_array($array))
            {
                array_splice($array, 0, $limit*$perpage);
                array_splice($array, $perpage);
            }
        }

    return $array;
    }

  /*
    Назначение: Функция TreeMenu занимется построения иерархического меню
    Параметры: $auth      (I) - идентификатор класса для выполн. запросов
               $idI       (I) - идентификатор узла c которого строится дерево
               $mode      (I) - режим: 0 - если текущий узел не надо выводить
                                       2 - вывод пути к выбранной вершине
                                       3 - вывод списка всех потомков страницы
                                       4 - вывод всех страниц (даже не активных)
               $find      (I) - идентификатор, который должны найти
               $parI      (I) - идентификатор его предка
               $nameI     (I) - имя узла
               $levI      (I) - его уровень
               $startMenu (S) - результирующий массив
               $popupMenu (S) - раскрытое меню
               $lock      (S) - режим блокировки дальнейшего углубления рекурсии
    Возвращает: $startMenu($popupMenu) - массив упорядоченных вершин дерева
    Описание: для построения меню из узла $idI необходимо вызвать данную
              функцию с проинициализированной переменной $auth класса
              CAuthorization и выбранным идентификатором этого узла
              (при запуске функции не должен быть заполнен 5-ый параметр)
              при запуске функции с параметром mode=2, путь будет возвращён
              в обратном порядке (не от корня дерева, а от искомого узла)
  */

  function TreeMenu($auth=0, $idI=0, $mode=0, $find=0, $parI=-1, $nameI='',
                    $levI=0, $descI='', $publI=0, $urlI='',
                    $small_flagI = 0, $small_nameI='', $small_sortI = 0, $imageI = 0)
  {
    static $startMenu, $popupMenu, $lock, $drop_node, $cnt_popup; // объявляем результ. массив и блокир.

    if($parI == -1)                       // если запускаем первый раз
    {                                     // инициализируем переменные
      $startMenu = array("id"=>null, "parent"=>null, "name"=>null,
                         "level"=>null, "descr"=>null, "publ"=>null, "url"=>null,
                         "small_flag"=>null, "small_name"=>null, "small_sort"=>null, "image"=>null);
      $popupMenu = array("id"=>null, "parent"=>null, "name"=>null,
                         "level"=>null, "descr"=>null, "publ"=>null, "url"=>null,
                         "small_flag"=>null, "small_name"=>null, "small_sort"=>null, "image"=>null);
      $lock = false;                      // по-умолчанию рекурсия разрешена
      $drop_node = 0;
      $cnt_popup = 0;

      // проверяем на наличие потомков
      $sql = "select count(id) from ".TBL_PREF."pages where id_parent=".$find;
      $cnt = $auth->QueryExecute($sql, 0);

      if($cnt[0] == 0)                    // если нет потомков то будем искать
      {                                   // своего родителя для раскрытия меню
        $sql = "select id_parent, level from ".TBL_PREF."pages where id=".$find;
        list($find_temp, $level) = $auth->QueryExecute($sql, array(0, 1));
        if($level[0] > 0) $find = $find_temp[0];
      }
    }

    if(($mode!=0))                        // если надо поместить узел в массив
      if($parI==0)                        // если это узел нулевого уровня
      {                                   // добавляем его в стартовое меню
        if($lock and !empty($popupMenu[0]["id"])/* and !$local_lock*/)
        {
          $startMenu = array_merge($startMenu, $popupMenu);
        }

        $startMenu[] = array("id"=>$idI, "parent"=>$parI,
                             "name"=>stripslashes($nameI), "level"=>$levI,
                             "descr"=>stripslashes($descI), "publ"=>$publI,
                             "url"=>stripslashes($urlI), "small_flag"=>$small_flagI,
                             "small_name"=>stripslashes($small_nameI), "small_sort"=>$small_sortI, "image"=>$imageI);

        $popupMenu = array("id"=>null, "parent"=>null, "name"=>null, "level"=>null,
                           "descr"=>null, "publ"=>null, "url"=>null,
                           "small_flag"=>null, "small_name"=>null, "small_sort"=>null, "image"=>null);
        $cnt_popup = 0;
      }
      else                                // иначе добавляем в выпадающее
      {
        if(!empty($publI) or ($mode==4))
        {
          $popupMenu[$cnt_popup] = array("id"=>$idI, "parent"=>$parI,
                                         "name"=>stripslashes($nameI), "level"=>$levI,
                                         "descr"=>stripslashes($descI), "publ"=>$publI,
                                         "url"=>stripslashes($urlI), "small_flag"=>$small_flagI,
                                         "small_name"=>stripslashes($small_nameI),
                                         "small_sort"=>$small_sortI, "image"=>$imageI);
          $cnt_popup++;
        }
      }

    if($lock and !empty($find)) return;   // блокировка рекурсии

    switch($mode)
    {
      case 2:                             // получаем путь к выбранной вершине
          if($find==$idI) $lock=true;     // если текущая вершина явл. искомой
          $sql = "select id_parent, id, name, level, description, published, url,
                         small_flag, small_name, small_sort, image
                  from ".TBL_PREF."pages where id=$idI and published=1
                  order by sort_index";
          break;

      case 4:
          $sql = "select id, id_parent, name, level, description, published, url,
                         small_flag, small_name, small_sort, image
                  from ".TBL_PREF."pages where id_parent=$idI
                  order by sort_index";
          break;

      default:                            // по-умолчанию строим дерево
          $sql = "select id, id_parent, name, level, description, published, url,
                         small_flag, small_name, small_sort, image
                  from ".TBL_PREF."pages where id_parent=$idI and not ((id_parent=0) and not published)
                  order by sort_index";
          break;
    }

    list($id, $par, $name, $lev, $desc, $publ, $url, $small_flag, $small_name, $small_sort, $image) =
        $auth->QueryExecute($sql, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

    for($i=0; $i<count($id); $i++)        // цикл по узлам опред. родителя
    {
      if($find==$par[$i]) $lock = true;   // если нашли узел - блокир. рекурсию

      if($parI != -1) $drop_node++;       // если проходим не по корневым вершинам

      TreeMenu($auth, $id[$i], $mode, $find, $par[$i], stripslashes($name[$i]),
               $lev[$i], stripslashes($desc[$i]), $publ[$i], stripslashes($url[$i]),
               $small_flag[$i], stripslashes($small_name[$i]), $small_sort[$i], $image[$i]);
    }

    if(!$lock)
    {
      for($i=0; $i<count($id); $i++)
        if(!empty($publ[$i]) and !empty($popupMenu[$cnt_popup-1]))
        {
          unset($popupMenu[$cnt_popup-1]);
          $cnt_popup--;
        }
    }

    // возвращаем готовое меню (путь) анализируя состояние рекурсии и режим
    if($parI == -1)                       // если это, не рекурсия
    {
      if($lock and isset($popupMenu[0]["id"]))
      {
        if(count($startMenu)==0)          // если нет стартового, то выводим
          return $popupMenu;              // выпадающее меню
        else                              // иначе объединяем старт. и выпадающ.
          return array_merge($startMenu, $popupMenu);
      }
      elseif(($mode==3) and empty($find)) // если выводим список всех страниц
        return array_merge($startMenu, $popupMenu);
      elseif($mode == 3)                  // если выводим список всех потомков
        return $popupMenu;                // вершины $find
      else return $startMenu;             // иначе только стартовое меню
    }
  }


  function findRoots($id)
  {
        $sql = mysql_query("SELECT id,id_parent FROM ".TBL_PREF."pages WHERE id='".$id."'");
        while($r = mysql_fetch_array($sql))
              if(!empty($r['id_parent'])) $result = findRoots($r['id_parent']);
              else $result = $r['id'];
       return $result;
  }

  function get_parent($id)
  {
          $q = mysql_query("SELECT id_parent FROM ".TBL_PREF."pages WHERE id='".$id."'");
          $r = mysql_fetch_array($q);
          return $r['id_parent'];
  }

  // возвращает массив, для раскрытого меню с заданным родителем(выводит только 2 уровня)
  // $id - идентификатор родителя, от которого строится дерево
  function treeMenuShowAll($auth,$id)
  {
   $sql = "SELECT p1.id, p1.name, p1.url, p1.level, p2.id, p2.name, p2.url, p2.level
           FROM ".TBL_PREF."pages p1
           LEFT JOIN ".TBL_PREF."pages p2 ON p1.id=p2.id_parent
           WHERE p1.id_parent='".$id."' and p1.published=1 and p2.published=1
           ORDER BY p1.sort_index,p2.sort_index";

   list($main_id, $main_name, $main_url, $main_level, $par_id, $par_name, $par_url, $par_level) =
        $auth->QueryExecute($sql, array(0, 1, 2, 3, 4, 5, 6, 7));

        foreach($main_id as $k=>$v)
        {
                $mainArr[] = array('main_id' => $v, 'main_name' => $main_name[$k], 'main_url' => $main_url[$k], 'main_level' => $main_level[$k]);
                $subArr[$v.'_'.$k] = array('par_id' => $par_id[$k], 'par_name' => $par_name[$k], 'par_url' => $par_url[$k], 'par_level' => $par_level[$k]);
        }

        foreach($mainArr as $k=>$v)
        {
                $array[$v['main_id']] = array('id'=>$v['main_id'] ,'name'=>$v['main_name'], 'url'=>$v['main_url'], 'level'=>$v['main_level'] );

                if(is_array($subArr[$v['main_id'].'_'.$k]))
                {
                     $array[$subArr[$v['main_id'].'_'.$k]['par_id']] = array('id'=>$subArr[$v['main_id'].'_'.$k]['par_id'], 'name'=>$subArr[$v['main_id'].'_'.$k]['par_name'],'url'=>$subArr[$v['main_id'].'_'.$k]['par_url'], 'level'=>$subArr[$v['main_id'].'_'.$k]['par_level']);
                }
        }

        foreach($array as $k=>$v)
                $menu[] = array('id'=>$v['id'], 'name'=>$v['name'], 'url'=>$v['url'], 'level'=>$v['level']);

        return $menu;
  }

  function openedMenu($start, $result=null){

        $q = mysql_query("SELECT * FROM ".TBL_PREF."pages WHERE id_parent='".$start."' ORDER BY sort_index");
        while($r = mysql_fetch_assoc($q)){

                $result[] = $r;
                $result = openedMenu($r['id'],$result);
        }
   return $result;
  }

  /*
    Назначение: Функция PageView занимается построением страницы
    Параметры: $auth    (I) - идентификатор класса для выполн. запросов
               $page_id (I) - идентификатор страницы
               $a_word  (I) - массив поисковых слов
               $idu, $headl, $text, $mod, $desc нужны для пред-просмотра
    Возвращает: список массивов текста страницы, заголовка, её опис. и кл. слов,
                в том случае если усановлен идентификатор страницы и она сущ.
    Описание: получает из БД страницу по её идентификатору и формирует её
  */

  function PageView($auth = 0, $page_id = 0, $a_word = '', $pageuri = 0, $idu = 0,
                    $headl = '', $text= '',  $mod = '', $desc = '')
  {
    global $publ, $priv, $bann;
    if(!empty($auth))
    {
      $txt = "";                          // текст страницы

      if(!empty($page_id))                // если берём данные о странице из БД
      {                                   // получение страницы
        $sql = "select * from ".TBL_PREF."pages where id=".$page_id;
        list($idu, $name, $headl, $text, $mod, $keyw, $desc, $publ, $priv,
             $bann, $url, $title, $id_g) =
            $auth->QueryExecute($sql, array(2, 3, 4, 5, 7, 9, 10, 11, 12,
                                            13, 15, 17, 18));

        // приведение переменных к "нормальному виду"
//        $headl = nl2br(stripslashes($headl[0]));
//        $url   = stripslashes($url[0]);
//        $text  = /*nl2br(*/stripslashes($text[0])/*)*/;
//        $text  = setBoldText($text[0], explode(" ", $a_word));
        $mod = $mod[0];
      }                                   // предварительный просмотр
      if(empty($page_id) AND !empty($pageuri))
      {

        $sql = "select * from ".TBL_PREF."pages where url='".$pageuri."'";
        list($idu, $name, $headl, $text, $mod, $keyw, $desc, $publ, $priv,
             $bann, $url, $title, $id_g) =
            $auth->QueryExecute($sql, array(2, 3, 4, 5, 7, 9, 10, 11, 12,
                                            13, 15, 17, 18));
        $mod = $mod[0];
      }
      else                                // выводим кнопку "Закрыть окно"
        $txt .= "<hr style='height: 3px;'>
                 <center>
                   <input type=button value='Закрыть окно' onclick='window.close();'>
                 </center>
                 <hr style='height: 3px;'>";

      $name  = ((!empty($name[0]))?stripslashes($name[0]):"");
      $headl = ((!empty($headl[0]))?nl2br(stripslashes($headl[0])):"");
      $text  = ((!empty($text[0]))?stripslashes($text[0]):"");
      $keyw  = ((!empty($keyw[0]))?stripslashes($keyw[0]):"");
      $desc  = ((!empty($desc[0]))?stripslashes($desc[0]):"");
      $url   = ((!empty($url[0]))?stripslashes($url[0]):"");
      $title = ((!empty($title[0]))?stripslashes($title[0]):"");
      $id_g  = ((!empty($id_g[0]))?$id_g[0]:0);

      $txt  = "<h1 id='h4'>$headl</h1>
               ".(($_SERVER['REQUEST_URI'] !== '/')?"<div class='pad30'>":"")."
                       ".((!empty($text))?
                             setBoldText(TextProcessing($auth, $text),
                                         explode(" ", $a_word),
                                           "black", "#FFCC00"):"")."
               ".(($_SERVER['REQUEST_URI'] !== '/')?"</div>":"")."<br>".
              ((!empty($id_g))?"<a href='".G_INDEX."?size=m&id=$id_g'>Посмотреть галерею изображений</a>":"")."";

      // возвращаем результаты
      return array($txt, $keyw, $desc, $title);
    }

    return array(null, null, null);
  }


  /*
    Назначение: Функция GetListFromArr формирует список из элементов масства
    Параметры: $arr (I)       - массив значений
               $separator (I) - разделитель
    Описание: получает $arr и проходя по всем его элементам строит список
              значений перечисленных через $separator
  */

  function GetListFromArr($arr = null, $separator = ",")
  {
    $res = '';

    if(!empty($arr))
      if(count($arr) > 0)
        foreach($arr as $key => $value)
          $res .= (($key==0)?"":$separator).$value;

    return $res;
  }


  /*
    Назначение: Функция TextProcessing обрабатывает в тексте найденные теги
    Параметры: $auth (I) - идентификатор класса для выполн. запросов
               $text (I) - текст для обработки
    Описание: в функцию передаётся текст в котором ищутся специальные теги [doc_
              и [img_ вместо которых вставляется HTML код <a> <img>.
              Значения для этих тегов берутся из БД.
  */

  function TextProcessing($auth = '', $text = '')
  {
    if(!empty($auth) and !empty($text))
    {
      // получение всех идентификаторов элементов включённых в текст
      $numbDoc = GetTagNumb($text, "[doc_");
      $numbImg = GetTagNumb($text, "[img_");
//      echo $numbDoc[0].$numbDoc[1]."<br>";
      // составление из этих идентификаторов элементов множеств
      $arrDoc = GetListFromArr($numbDoc);
      $arrImg = GetListFromArr($numbImg);

//      echo "docs - ".$arrDoc."<br>images - ".$arrImg;
      // получение информации о элементах входящих в множества
      if(!empty($arrDoc))
      {
        list($id, $name, $path) =
            $auth->QueryExecute("select * from ".TBL_PREF."docs where id in (".$arrDoc.")",
                                array(0, 1, 2));
        if(!empty($id[0]))
          foreach($id as $key => $value)
            $text = str_replace("[doc_".$value."]",
                                "<a href='".DOC_URL.$path[$key]."'>".$name[$key]."</a>",
                                $text);
      }

      if(!empty($arrImg))
      {
        list($id, $alt, $align, $path) =
            $auth->QueryExecute("select * from ".TBL_PREF."images where id in (".$arrImg.")",
                                array(0, 1, 2, 3));
        if(!empty($id[0]))
          foreach($id as $key => $value)
            $text = str_replace("[img_".$value."]",
                                "<img alt='".$alt[$key]."' align=".$align[$key].
                                    " src='".IMG_URL.$path[$key]."' class=images hspace=5 vspace=4>", $text);
      }
    }

    return $text;
  }


  /*
    Назначение: Функция SiteMap занимается построением карты сайта
    Параметры: $auth (I) - идентификатор класса для выполн. запросов
               $mode (I) - режим отображения страниц аналогично $mode TreeMenu
    Описание: вывод раскрытое меню
  */

  function SiteMap($auth = 0, $mode = 1, $first = 0, $input = 'chbox')
  {
    global $cPath, $cID, $pages_array, $moved_id, $sort_id, $sort_name;

    $cPath = !empty($cPath)?$cPath:0; $cnt = 0;

    if(!empty($auth))
    {
      $menu = TreeMenu($auth, $first, ($mode!=6?$mode:4), 0);  // получение карты сайта
?>                    <div id="dataTable">
                       <table border=0 width=100% cellspacing=0 cellpadding=0>
                        <tr>
                          <th align=left>&nbsp;
                          <th align=left>Название</th>
                          <th align=left>Адрес</th>
                          <th align=center>Отображение</th>
                          <th align=center>Действия</th>
                        </tr>
<?
      for($i=0; $i<count($menu); $i++)
        if(isset($menu[$i]["id"]))
          switch($mode)                           // режим отображения страницы
          {
            case 6:                               // начинаем форм. список стр.
                if ($i == 0)
                {
?>
    <tr class="bold_line"><td>Название</td><td>Короткое название</td><td>Индекс сортировки</td><td>Отображать в верхнем меню</td></tr>
<?              } ?>
    <tr>
      <td>
        <input name=page_id[<?=$i;?>] type=hidden value="<?=$menu[$i]['id'];?>">
        <?=str_repeat("&nbsp", ($menu[$i]["level"]+1)*3);?>
        <?=$menu[$i]["name"];?>
      </td>
      <td align=center>
        <input name=small_name[<?=$i;?>] value="<?=$menu[$i]['small_name'];?>" size=30>
      </td>
      <td align=center>
        <input name=small_sort[<?=$i;?>] value="<?=$menu[$i]['small_sort'];?>" size=7>
      </td>
      <td align=center>
        <input name=small_flag[<?=$i;?>] type=checkbox <?=($menu[$i]['small_flag']?"checked":"");?> value="1">
      </td>
    </tr>
<?
                break;

            case 4:                               // отображение в администр.
              if ($menu[$i]['parent'] == $cPath)
              {
                if (empty($cID)) $cID = $menu[$i]["id"];

                  $cnt++;

                  echo '<tr onmouseover="rowOverEffect(this, '.($cnt%2).')" onmouseout="rowOutEffect(this, '.($cnt%2).')" class="dataTableRow'.($cnt%2).'"'.'>'."\n";
?>
                          <td align="center">
                            <div><a href="?action=up_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="Вверх"><img src="images/icons/up.gif" height="6" width="8" border=0 alt="Вверх"></a></div>
                            <div style="padding-top:3px;"><a href="?action=down_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="Вниз"><img src="images/icons/down.gif" height="6" width="8" border=0 alt="Вниз"></a></div>
                          </td>
                          <td>
                            <?='<a href="?selected_box=page&action=pages&cPath='.$menu[$i]["id"].'">'.
                               $menu[$i]["name"].'</a>';?>
                          </td>
                          <td align="left"><small><?=(!empty($menu[$i]["url"])?$menu[$i]["url"]:"/index.php?page_id=".$menu[$i]["id"]);?></small></td>
                          <td align="center">
<? if (empty($menu[$i]["publ"])){ ?>
                            <a href="?action=unlock_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>"><img src="images/icon_status_green_light.gif" alt="Активизировать" title=" Активизировать " width="18" border="0" height="18"></a>&nbsp;&nbsp;&nbsp;
                            <img src="images/icon_status_red.gif" alt="Неактивный" title=" Неактивный " width="18" border="0" height="18">
<? } else { ?>
                            <img src="images/icon_status_green.gif" alt="Активный" title=" Активный " width="18" border="0" height="18">&nbsp;&nbsp;&nbsp;
                            <a href="?action=lock_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>"><img src="images/icon_status_red_light.gif" alt="Сделать неактивным" title=" Сделать неактивным " width="18" border="0" height="18"></a>
<? } ?>
                          </td>
                          <td align="center">
<? if (!empty($moved_id) and ($moved_id==$menu[$i]['id'])) { ?>
                            <form action="?action=pages" method="get" style="display:inline;">
                              <input type=hidden name="action" value="move_page">
                              <input type=hidden name="page_id" value="<?=$moved_id;?>">
                              <select name="cPath" onChange="if(confirm('Вы действительно хотите переместить страницу?')) this.form.submit();">
                                <option value=0>Начало</option>
                                <?=CreateSelOption($sort_id, $sort_name, !empty($cPath)?$cPath:0);?>
                              </select>
                            </form>
<? } else { ?>
                            <a href="?action=edit_page&page_id=<?=$menu[$i]['id'];?>" title="Редактровать"><img src="images/icons/ico_edit.gif" height="22" width="22" border=0 alt="Редактировать"></a>&nbsp;&nbsp;&nbsp;
                            <a href="?selected_box=page&action=pages&cPath=<?=$cPath;?>&moved_id=<?=$menu[$i]['id'];?>" title="Переместить"><img src="images/icons/move.gif" height="22" width="43" border=0 alt="Переместить"></a>&nbsp;&nbsp;&nbsp;
<?  if (empty($pages_array[$menu[$i]["id"]])) { ?>
                            <a href="?action=del_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="Удалить" onClick="if(confirm('Вы действительно хотите удалить?')) return true; else return false;"><img src="images/icons/ico_del.gif" height="22" width="22" border=0 alt="Удалить"></a>
<?  } else { ?>
                            <img src="images/icons/b_cancel.gif" height="22" width="22" border=0 alt="Нельзя удалить - удалите связанные страницы" title="Нельзя удалить - удалите связанные страницы">
<?
    }
  }
?>
                            &nbsp;
                          </td>
                        </tr>
<?
              }
              break;

            case 3:
                if($i == 0)                       // добавление недостающих
                {                                 // данных для главных узлов
                  $sql = "select name, description, level, id_parent
                          from ".TBL_PREF."pages where id=".$menu[$i]["id"];
                  list($name, $desc, $lev, $parent) =
                      $auth->QueryExecute($sql, array(0, 1, 2, 3));

                  if(isset($name[0]) and isset($desc[0]) and
                     isset($lev[0])  and isset($parent[0]))
                  {
                    $menu[$i]["level"]  = $lev[0];
                    $menu[$i]["name"]   = $name[0];
                    $menu[$i]["descr"]  = $desc[0];
                    $menu[$i]["parent"] = $parent[0];
                  }
                }
?>
    <tr>
      <td>
        <?=str_repeat("&nbsp", ($menu[$i]["level"]+1)*INDENT);?>
<?              if($input == 'chbox'){ ?>
        <input name=node type=checkbox value=<?=$menu[$i]["id"];?>
               onclick="ChildDisable(<?=$menu[$i]['id'];?>, this.checked);">
<?              }else{ ?>
        <input name=node type=radio value=<?=$menu[$i]["id"];?>>
<?              } ?>
        <input name=parent<?/*=$menu[$i]["id"];*/?> type=hidden value=<?=$menu[$i]["parent"];?>>
        <?=$menu[$i]["name"];?> - <small><?=$menu[$i]["descr"];?></small>
      </td>
    </tr>
<?
                break;

            default:
?>
    <tr>
      <td>
        <?=str_repeat("&nbsp", ($menu[$i]["level"]+1)*INDENT);?>
        <input name=node type=radio value='<?=$menu[$i]["id"];?>'>
        <?=$menu[$i]["name"];?> - <small><?=$menu[$i]["descr"];?></small>
      </td>
    </tr>
<?
          }

?> </table></div> <?

   }
  }


  /*
    Назначение: Функция GetTagNumb возвращет числа заключенные в специалные теги
    Параметры: $text      (I) - текст в котором осуществляется поиск
               $begin_tag (I) - открывающий тег
               $end_tag   (I) - закрывающий тег
    Описание: функция возвращает массив значений содержащихся в тексте $text,
              находящийся между тегами $begin_tag и $end_tag
  */

  function GetTagNumb($text = '', $begin_tag = '', $end_tag = ']')
  {
    if(!empty($text) and !empty($begin_tag) and !empty($end_tag))
    {
      $res = array();                     // результирующий массив
      $tok = explode($begin_tag, $text);  // получение массива вхождений

      // цикл по всем вхождениям c получением значения до закрывающего тега
      for($i=1; $i<count($tok); $i++)
        $res[] = substr($tok[$i], 0, strpos($tok[$i], $end_tag));

      return $res;                        // возвращение результата
    }
  }


  /*
    Назначение: Функция HTMLAddDoc - форма для прикрепления файлов
    Описание: отображает элементы для добавления файлов
  */

  function HTMLAddDoc()
  {
?>
            <tr>
              <td style="padding-right:5px;">
                <script language='JavaScript'>
                  document.writeln(InputDocEls());
                </script>
              </td>
              <td nowrap="nowrap" valign=bottom>
                <input type=button name=drop value=" &minus; " onclick="dropFile(this);">
                <input type=button value=" + " onclick="addFile(this);">
              </td>
            </tr>
<?
  }


  /*
    Назначение: Функция HTMLAddImg - форма для прикрепления изображений
    Описание: отображает элементы для добавления изображений
  */

  function HTMLAddImg()
  {
?>
            <tr>
              <td style="padding-right:5px;">
                <script language='JavaScript'>
                  document.writeln(InputImgEls());
                </script>
              </td>
              <td nowrap="nowrap" valign=bottom>
<!--                <input type=button name=dropImage value=" &minus; " onclick="dropImage(this);"> -->
                <input type=button name=dropImage value=" &minus; " onclick="dropImg(this);">
                <input type=button value=" + " onclick="addImage(this);">
              </td>
            </tr>
<?
  }


  /*
    Назначение: Функция AttachedFiles - отмечает галочками прикрепленные файлы
    Параметры: $arr - массив прикреплённых файлов
               $name - будущее начало названия флажочка
               $begin_tag - открывающий тег (для отображения)
               $end_tag - закрывающий тег (для отображения)
    Описание: отображает галочками элементы из массива $arr. Отображение происх.
              как открывающий тег + значение из массива + закрывающий тег.
  */

  function AttachedFiles($arr='', $name, $begin_tag = '', $end_tag = ']')
  {
    if(!empty($arr))                      // если обновляем страницу
      foreach($arr as $key => $value)     // и имеются файлы
      {                                   // покажем флажочками их наличие
?>
        <tr>
          <td colspan=2>
            <input name=<?=$name.$value;?> type=checkbox value=<?=$value;?> checked>
            <?=$begin_tag.$value.$end_tag;?>
          </td>
        </tr>
<?
      }
  }


  /*
    Назначение: Функция CreateSelOption - создаёт эл-ты выпадающего списка
    Параметры: $id   (I) - идентификаторы позиций (значение value)
               $name (I) - названия позиций (будут отображаться)
    Описание: заполняет эл-ты вападающего списка
  */

  function CreateSelOption($id, $name, $def=-1)
  {
    foreach($id as $key => $value)
      print "<option value='".$value."'".(($def==$value)?" selected":"")." class=tbl_txt>".
            stripslashes($name[$key])."</option>";
  }

  // создаёт элементы option, начиная с nFirst и заканчивая nLast
  function CreateSelOption1($nFirst = 0, $nLast = 0, $arr, $iSelected = 0)
  {
    $res = "";
    for($i=$nFirst; $i<=$nLast; $i++)
    {
      $res .= "<br><option value='".$i."'".(($i==$iSelected)?" selected":"").">";
      if(isset($arr[$i])) $res .= stripslashes($arr[$i]);
      else $res .= $i;
      $res .= " </option>";
    }
    return $res;
  }

  /*
    Назначение: Функция PageMove - перемещает страницу
    Параметры: $auth      (I) - идентификатор класса для выполн. запросов
               $id        (I) - идентификатор страницы
               $direction (I) - направление перемещения страницы
                                up   - наверх
                                down - вниз
    Описание: перемещает страницу, в иерархии таблиц своего уровня для одного и
              того же предка, в направлении $direction
  */

  function PageMove($auth = null, $id = 0, $direction = '')
  {
    if(!empty($auth) and !empty($id) and!empty($direction))
    {
      $sql = "select id, id_parent, sort_index from ".TBL_PREF."pages where id=".$id;
      list($id1, $parent, $s_ind) = $auth->QueryExecute($sql, array(0, 1, 2));

      // если существует перемещаемая страница с идентификатором $id
      if(!empty($id1[0]) and !empty($s_ind[0]))
        switch($direction)                // определение направления перемещения
        {
          case "up":                      // перемещение страницы наверх
              $sql = "select id, sort_index from ".TBL_PREF."pages
                      where id_parent=$parent[0] and sort_index<$s_ind[0]
                      ORDER BY sort_index DESC";
              list($id2, $sort_index2) = $auth->QueryExecute($sql, array(0,1));

              if(!empty($id2[0]))         // если есть стр. выше перемещаемой
              {                           // изменяем их индексы сортировки
                $sql = "update ".TBL_PREF."pages set sort_index=";
                $auth->QueryExecute($sql."$sort_index2[0] where id=$id1[0]");
                $auth->QueryExecute($sql."$s_ind[0]   where id=$id2[0]");
              }
              break;

          case "down":                    // перемещение страницы вниз
              $sql = "select id, sort_index from ".TBL_PREF."pages
                      where id_parent=$parent[0] and sort_index>$s_ind[0]
                      ORDER BY sort_index";
              list($id2, $sort_index2) = $auth->QueryExecute($sql, array(0,1));

              if(!empty($id2[0]))         // если есть стр. ниже перемещаемой
              {                           // изменяем их индексы сортировки
                $sql = "update ".TBL_PREF."pages set sort_index=";
                $auth->QueryExecute($sql."$sort_index2[0] where id=$id1[0]");
                $auth->QueryExecute($sql."$s_ind[0]   where id=$id2[0]");
              }
              break;
        }
    }
  }


  /*
    Назначение: Функция find - осуществляет поиск информации в новостях и на страницах
    Параметры: $auth    (I) - идентификатор класса для выполн. запросов
               $words   (I) - ключ поиска
               $place   (I) - место, где необходимо искать
               $arr_res (I) - массив, определяющий, что будет являться результ.
                              0 - идентификатор
                              1 - название
                              2 - короткий текст
                              3 - полный текст
                              4 - дата
    Возвращает: массив из идентификаторов и наименований
    Описание: осуществяет поиск $words в $place
  */

  function find($auth = null, $words = null, $place = null, $arr_res = null)
  {
    if(!empty($words) and !empty($place) and !empty($auth) and !empty($arr_res))
    {
      $search = "%".str_replace(" ", "%", $words)."%";

      switch($place)
      {
        case "page":
            $sql = "select id, name, headline, story_text, modified from ".TBL_PREF."pages
                    where ((keywords like '$search') or (headline like '$search') or
                           (name like '$search') or (description like '$search') or
                           (story_text like '$search')) and (published = 1)";
            break;

        case "news":
            $sql = "select id, title, intro, text, date from ".TBL_PREF."news
                    where ((text like '$search') or (title like '$search') or
                           (intro like '$search')) and
                           (date <= '".date("Y-m-d")."')";
            break;

        default: return null;
      }

      return $auth->QueryExecute($sql, $arr_res);
    }
    else return null;
  }

  function hdLocalStat($txt1='', $cnt1=0, $txt2='', $cnt2=0, $txt3='', $cnt3=0)
  {
?>
      <br><b><div style="margin-left: 150px"><?=date("d.m.Y");?></div></b><br>
      <div style="margin-left: 90px"><img src="images/letter.gif" width=18 height=13> <?=$txt1;?> <b><?=$cnt1;?></b></div>
      <div style="margin-left: 90px"><img src="images/warning.gif" width=18 height=17> <?=$txt2;?> <b><?=$cnt2;?></b></div>
      <div style="margin-left: 90px"><img src="images/warning.gif" width=18 height=17> <?=$txt3;?> <b><?=$cnt3;?></b></div>
<?
  }

  function normDT($dt = null)
  {
    if(!empty($dt))
    {
      $dt = explode("-", $dt);
      if(count($dt) == 3) return $dt[2]."-".$dt[1]."-".$dt[0];
    }

    return "";
  }


  /*
    Назначение: Функция setBoldText - выделяет найденные слова в тексте
    Параметры: $txt    (I) - текст
               $a_word (I) - массив слов для поиска в тексте
               $color  (I) - цвет текста, к. необх. выделять найденный текст
               $bg     (I) - цвет фона текста, к. необх. выделять найденный текст
    Возвращает: текст $txt с выделенными в нём словами из массива $a_word
    Описание: обрамляет тегами <b></b> в тексте $txt все слова из
              массива $a_word и возвращает этот изменённый текст
  */
  function setBoldText($txt = '', $a_word = null, $color = "black", $bg = "white")
  {
    if(!empty($txt) and !empty($a_word[0]))
      foreach($a_word as $key => $value)  // цикл по словам ключевой фразы
        if(!empty($value))                // для того, чтобы ключевые слова
        {                                 // выделить жирным
          $pos = 0;
          while($pos = strpos($txt, $value, $pos))
          {
            $txt  = substr_replace($txt, "<font style='color: $color; background-color: $bg;'><b>".$value."</b></font>", $pos, strlen($value));
            $pos += strlen("<font style='color: $color; background-color: $bg;'><b>".$value."</b></font>");
          }
        }

    return $txt;
  }

  function viewPages($cnt = 1, $limit_begin = 1,
                     $gallery_limit = 1, $page_gallery = 5, $href = '', $filter = '')
  {
    $page_count = ceil($cnt/$gallery_limit); // кол-во стр. новостей (всего)
    $first_page = (($limit_begin-$page_gallery) > 0) ?
                   ($limit_begin-$page_gallery) : 0;
    $last_page  = (($limit_begin+$page_gallery) < ($page_count-1)) ?
                   ($limit_begin+$page_gallery) : ($page_count-1);

    if($cnt > $gallery_limit)                // если не помещаются
    {                                           // на один лист
      /*
      $html .= "<nobr>";
      if($limit_begin!=0)
        $html .= "<a href='".$href.($limit_begin-1)."'>"."&#171;-</a> Предыдущие";
      print " ";
      if($limit_begin!=($page_count-1))
        $html .= "Следующие <a href='".$href.($limit_begin+1)."'>"."-&#187;</a>";
      $html .= "</nobr><br>";
      */

      if (!empty($filter))
          $form .= '<form name="listing" action="'.$href.'1" method="post">'.$filter.'</form>';

      for($i=$first_page; $i<=$last_page; $i++)
      {
        if($i!=$limit_begin)
        {
          if (!empty($filter))
              $str = "<a href='javascript: void[0];' alt='".$href.$i."' onMouseOver='change_listing_action(this)' onClick='submit_listing();'>";
          else
              $str = "<a href='".$href.$i."' alt=''>";

          if((($i==$first_page) and ($i!=0)) or
             (($i==$last_page)  and ($i!=($page_count-1))))
            $str .= "...";
          else
            $str .= ($i+1);
          $html .= ($str."</a>");
        }
        else $html .= "<span class='active' style='background: #DCDCDC'>".($i+1)."</span>";
        $html .= " ";
      }
    }else $html .= "<span class='active' style='background: #DCDCDC'>1</span>";
  return $form.'<p>'.$html.'</p>';
  }

/*
  function viewPages($tbl_name = '', $auth = null, $limit_begin = 1,
                     $gallery_limit = 1, $page_gallery = 5, $href = '')
  {
    $sql = "SELECT COUNT(*) FROM $tbl_name";

    list($id_cnt) = $auth->QueryExecute($sql, 0);

    $page_count = ceil($id_cnt/$gallery_limit); // кол-во стр. новостей (всего)
    $first_page = (($limit_begin-$page_gallery) > 0) ?
                   ($limit_begin-$page_gallery) : 0;
    $last_page  = (($limit_begin+$page_gallery) < ($page_count-1)) ?
                   ($limit_begin+$page_gallery) : ($page_count-1);

    if($id_cnt > $gallery_limit)                // если не помещаются
    {                                           // на один лист
      print "<nobr>";
      if($limit_begin!=0)
        print "<a href='".$href.($limit_begin-1)."'>"."&#171;-</a> Предыдущие";
      print " ";
      if($limit_begin!=($page_count-1))
        print "Следующие <a href='".$href.($limit_begin+1)."'>"."-&#187;</a>";
      print "</nobr><br>";

      for($i=$first_page; $i<=$last_page; $i++)
      {
        if($i!=$limit_begin)
        {
          $str = "<a href='".$href.$i."'>";
          if((($i==$first_page) and ($i!=0)) or
             (($i==$last_page)  and ($i!=($page_count-1))))
            $str .= "...";
          else
            $str .= ($i+1);
          print ($str."</a>");
        }
        else
          print "<span class='active' style='background: #DCDCDC'>".($i+1)."</span>";
        print " ";
      }
    }
  }
 */
  /*
    Назначение: Функция FixedLineFromArr - выводит текст в линию на основе массива
    Параметры: $arr       (I) - массив текстовых значений
               $width     (I) - максимальная ширина строки
               $separator (I) - разделители в выходной строке между значениями
                                элементов массива $arr
    Описание: возвращает текстовую строку построенную на основе значений
              переданных в массиве $arr, между которых будут стоять разделители
              $separator. Длина выходной строки не будет превышать $width.
  */

  function FixedLineFromArr($btag, $body, $etag, $width = 0, $color = "white")
  {
    $res = "";  $out = "";

    if(!empty($body) and !empty($width))
      if(count($body) > 0)                 // если $arr - массив
        foreach($body as $key => $value)
          if(empty($res))                 // если первый элемент
          {
            if(strlen($value) < $width)
            {
              $out = $btag[$key].$value.$etag[$key];
              $res = $value;
            }
          }
          else                            // если в $res уже есть значения
          {
            if(strlen($res.$value) < $width)
            {
              $out = $out.$btag[$key].$value.$etag[$key];
              $res = $res.$value;
            }
          }
    return $out;
  }


  function top_menu($auth = null, $top_menu_width = 900, $color = "white")
  {
    $sql = "select id, small_name from ".TBL_PREF."pages
            where small_flag=1 order by small_sort";
    list($page_id, $small_name) = $auth->QueryExecute($sql, array(0, 1));

    if(!empty($page_id[0]))
    {
      $btag = array();  $body = array();  $etag = array();

      foreach($page_id as $key => $value)
      {
        $btag[] = "<li><a href='index.php?page_id=".$value."'>";
        $body[] = $small_name[$key];
        $etag[] = "</a></li>";
      }

      return FixedLineFromArr($btag, $body, $etag, $top_menu_width, $color);
    }
  }

  function structure($auth = null, $where = "1")
  {
    $result = array();

    $sql = "SELECT id, id_parent, name, level FROM ".TBL_PREF."pages WHERE ".$where." ORDER BY sort_index";
    list($id, $id_parent, $name, $level) = $auth->QueryExecute($sql, array(0, 1, 2, 3));

    for ($i=0; $i<count($id); $i++)
      $result[$id_parent[$i]][$id[$i]] = array("name"=>$name[$i], "level"=>$level[$i]);

    return $result;
  }

  function sort_structure($array, $parent, $level=0)
  {
    global $sort_id, $sort_name;

    if (!empty($array[$parent])) foreach ($array[$parent] as $key => $value)
    {
      $sort_id[] = $key;
      $sort_name[] = str_repeat("&nbsp;|&nbsp;", $level+1).substr($value["name"], 0, 25);
      sort_structure($array, $key, $level+1);
    }
  }


/* $param=1 - 3 новости в блоке
   $param=2 - все новости списком
   $param=3 - конкретная новость
*/
  function getNews($param){

          if(file_exists(NEWS_SCRIPT_DIR."news.php")){

                  require_once(NEWS_SCRIPT_DIR."news.php");

                  if($param == 1) $html = getCurentNews($_REQUEST['newsId']);
                  if($param == 2) $html = getNewsInBlock();
                  if($param == 3) $html = getNewsArchive();
          }

  return $html;
  }

/* $param=1 - список подключенных к материалу галлерей
   $param=2 - отображать выбранную галерею
   $param=3 - страница картинки
   $case - тип материала
*/
  function getGallery($auth,$param,$case='')
  {
          if(file_exists(GALLERY_SCRIPT_DIR."gallery.php")){

                  require_once(GALLERY_SCRIPT_DIR."gallery.php");

                  if($param == 1) $html = getGalleryList($auth,$param,$case);
                  if($param == 2) $html = getCurentGallery($auth);
                  if($param == 3) $html = getCurentImage($auth);
                  if($param == 4) $html = getGalleryList($auth,$param,$case);
          }

  return $html;
  }

/*
  $param=1 выводит каталог
  $param=2 выводит меню каталога
  $param=3 инфо продукта
*/
  function getCatalog($auth,$param,$start='',$idCat='',$idProd='', $filter = '')
  {
      if(file_exists(CATALOG_SCRIPT_DIR."catalog.php"))
      {
          require_once(CATALOG_SCRIPT_DIR."catalog.php");

          if ($param == 1) $html = getAllCatalog($auth);
          if ($param == 2) $html = catalogMenu($auth,$start,$idCat);
          if ($param == 3) $html = getProduct($auth,$idCat,$idProd);
          if ($param == 4) $html = getIndexView($auth);
          if ($param == 5) $html = getInnerView($auth, $param, $filter);
      }

  return $html;
  }

  function getBanners($auth, $category = '', $limit = '')
  {
      if (file_exists(BANNER_SCRIPT_DIR."banners.php"))
      {
          require_once (BANNER_SCRIPT_DIR."banners.php");

          $html = getBanner($auth, $category, $limit);
      }

  return $html;
  }

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                $level--;
                $ends_line_level = NULL;
                $new_line_level = $level;
                break;

                case '{': case '[':
                $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                $char = "";
                $ends_line_level = $new_line_level;
                $new_line_level = NULL;
                break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

?>