<?
// ������� ���������� �������� ���� , �� �������� �������� ������;

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

/*  �������� ����� ������� � ������, ������������� ��� ��� ����� � ������������� ������
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
    ����������: ������� TreeMenu ��������� ���������� �������������� ����
    ���������: $auth      (I) - ������������� ������ ��� ������. ��������
               $idI       (I) - ������������� ���� c �������� �������� ������
               $mode      (I) - �����: 0 - ���� ������� ���� �� ���� ��������
                                       2 - ����� ���� � ��������� �������
                                       3 - ����� ������ ���� �������� ��������
                                       4 - ����� ���� ������� (���� �� ��������)
               $find      (I) - �������������, ������� ������ �����
               $parI      (I) - ������������� ��� ������
               $nameI     (I) - ��� ����
               $levI      (I) - ��� �������
               $startMenu (S) - �������������� ������
               $popupMenu (S) - ��������� ����
               $lock      (S) - ����� ���������� ����������� ���������� ��������
    ����������: $startMenu($popupMenu) - ������ ������������� ������ ������
    ��������: ��� ���������� ���� �� ���� $idI ���������� ������� ������
              ������� � ��������������������� ���������� $auth ������
              CAuthorization � ��������� ��������������� ����� ����
              (��� ������� ������� �� ������ ���� �������� 5-�� ��������)
              ��� ������� ������� � ���������� mode=2, ���� ����� ���������
              � �������� ������� (�� �� ����� ������, � �� �������� ����)
  */

  function TreeMenu($auth=0, $idI=0, $mode=0, $find=0, $parI=-1, $nameI='',
                    $levI=0, $descI='', $publI=0, $urlI='',
                    $small_flagI = 0, $small_nameI='', $small_sortI = 0, $imageI = 0)
  {
    static $startMenu, $popupMenu, $lock, $drop_node, $cnt_popup; // ��������� �������. ������ � ������.

    if($parI == -1)                       // ���� ��������� ������ ���
    {                                     // �������������� ����������
      $startMenu = array("id"=>null, "parent"=>null, "name"=>null,
                         "level"=>null, "descr"=>null, "publ"=>null, "url"=>null,
                         "small_flag"=>null, "small_name"=>null, "small_sort"=>null, "image"=>null);
      $popupMenu = array("id"=>null, "parent"=>null, "name"=>null,
                         "level"=>null, "descr"=>null, "publ"=>null, "url"=>null,
                         "small_flag"=>null, "small_name"=>null, "small_sort"=>null, "image"=>null);
      $lock = false;                      // ��-��������� �������� ���������
      $drop_node = 0;
      $cnt_popup = 0;

      // ��������� �� ������� ��������
      $sql = "select count(id) from ".TBL_PREF."pages where id_parent=".$find;
      $cnt = $auth->QueryExecute($sql, 0);

      if($cnt[0] == 0)                    // ���� ��� �������� �� ����� ������
      {                                   // ������ �������� ��� ��������� ����
        $sql = "select id_parent, level from ".TBL_PREF."pages where id=".$find;
        list($find_temp, $level) = $auth->QueryExecute($sql, array(0, 1));
        if($level[0] > 0) $find = $find_temp[0];
      }
    }

    if(($mode!=0))                        // ���� ���� ��������� ���� � ������
      if($parI==0)                        // ���� ��� ���� �������� ������
      {                                   // ��������� ��� � ��������� ����
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
      else                                // ����� ��������� � ����������
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

    if($lock and !empty($find)) return;   // ���������� ��������

    switch($mode)
    {
      case 2:                             // �������� ���� � ��������� �������
          if($find==$idI) $lock=true;     // ���� ������� ������� ���. �������
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

      default:                            // ��-��������� ������ ������
          $sql = "select id, id_parent, name, level, description, published, url,
                         small_flag, small_name, small_sort, image
                  from ".TBL_PREF."pages where id_parent=$idI and not ((id_parent=0) and not published)
                  order by sort_index";
          break;
    }

    list($id, $par, $name, $lev, $desc, $publ, $url, $small_flag, $small_name, $small_sort, $image) =
        $auth->QueryExecute($sql, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

    for($i=0; $i<count($id); $i++)        // ���� �� ����� �����. ��������
    {
      if($find==$par[$i]) $lock = true;   // ���� ����� ���� - ������. ��������

      if($parI != -1) $drop_node++;       // ���� �������� �� �� �������� ��������

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

    // ���������� ������� ���� (����) ���������� ��������� �������� � �����
    if($parI == -1)                       // ���� ���, �� ��������
    {
      if($lock and isset($popupMenu[0]["id"]))
      {
        if(count($startMenu)==0)          // ���� ��� ����������, �� �������
          return $popupMenu;              // ���������� ����
        else                              // ����� ���������� �����. � ��������.
          return array_merge($startMenu, $popupMenu);
      }
      elseif(($mode==3) and empty($find)) // ���� ������� ������ ���� �������
        return array_merge($startMenu, $popupMenu);
      elseif($mode == 3)                  // ���� ������� ������ ���� ��������
        return $popupMenu;                // ������� $find
      else return $startMenu;             // ����� ������ ��������� ����
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

  // ���������� ������, ��� ���������� ���� � �������� ���������(������� ������ 2 ������)
  // $id - ������������� ��������, �� �������� �������� ������
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
    ����������: ������� PageView ���������� ����������� ��������
    ���������: $auth    (I) - ������������� ������ ��� ������. ��������
               $page_id (I) - ������������� ��������
               $a_word  (I) - ������ ��������� ����
               $idu, $headl, $text, $mod, $desc ����� ��� ����-���������
    ����������: ������ �������� ������ ��������, ���������, � ����. � ��. ����,
                � ��� ������ ���� ��������� ������������� �������� � ��� ���.
    ��������: �������� �� �� �������� �� � �������������� � ��������� �
  */

  function PageView($auth = 0, $page_id = 0, $a_word = '', $pageuri = 0, $idu = 0,
                    $headl = '', $text= '',  $mod = '', $desc = '')
  {
    global $publ, $priv, $bann;
    if(!empty($auth))
    {
      $txt = "";                          // ����� ��������

      if(!empty($page_id))                // ���� ���� ������ � �������� �� ��
      {                                   // ��������� ��������
        $sql = "select * from ".TBL_PREF."pages where id=".$page_id;
        list($idu, $name, $headl, $text, $mod, $keyw, $desc, $publ, $priv,
             $bann, $url, $title, $id_g) =
            $auth->QueryExecute($sql, array(2, 3, 4, 5, 7, 9, 10, 11, 12,
                                            13, 15, 17, 18));

        // ���������� ���������� � "����������� ����"
//        $headl = nl2br(stripslashes($headl[0]));
//        $url   = stripslashes($url[0]);
//        $text  = /*nl2br(*/stripslashes($text[0])/*)*/;
//        $text  = setBoldText($text[0], explode(" ", $a_word));
        $mod = $mod[0];
      }                                   // ��������������� ��������
      if(empty($page_id) AND !empty($pageuri))
      {

        $sql = "select * from ".TBL_PREF."pages where url='".$pageuri."'";
        list($idu, $name, $headl, $text, $mod, $keyw, $desc, $publ, $priv,
             $bann, $url, $title, $id_g) =
            $auth->QueryExecute($sql, array(2, 3, 4, 5, 7, 9, 10, 11, 12,
                                            13, 15, 17, 18));
        $mod = $mod[0];
      }
      else                                // ������� ������ "������� ����"
        $txt .= "<hr style='height: 3px;'>
                 <center>
                   <input type=button value='������� ����' onclick='window.close();'>
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
              ((!empty($id_g))?"<a href='".G_INDEX."?size=m&id=$id_g'>���������� ������� �����������</a>":"")."";

      // ���������� ����������
      return array($txt, $keyw, $desc, $title);
    }

    return array(null, null, null);
  }


  /*
    ����������: ������� GetListFromArr ��������� ������ �� ��������� �������
    ���������: $arr (I)       - ������ ��������
               $separator (I) - �����������
    ��������: �������� $arr � ������� �� ���� ��� ��������� ������ ������
              �������� ������������� ����� $separator
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
    ����������: ������� TextProcessing ������������ � ������ ��������� ����
    ���������: $auth (I) - ������������� ������ ��� ������. ��������
               $text (I) - ����� ��� ���������
    ��������: � ������� ��������� ����� � ������� ������ ����������� ���� [doc_
              � [img_ ������ ������� ����������� HTML ��� <a> <img>.
              �������� ��� ���� ����� ������� �� ��.
  */

  function TextProcessing($auth = '', $text = '')
  {
    if(!empty($auth) and !empty($text))
    {
      // ��������� ���� ��������������� ��������� ���������� � �����
      $numbDoc = GetTagNumb($text, "[doc_");
      $numbImg = GetTagNumb($text, "[img_");
//      echo $numbDoc[0].$numbDoc[1]."<br>";
      // ����������� �� ���� ��������������� ��������� ��������
      $arrDoc = GetListFromArr($numbDoc);
      $arrImg = GetListFromArr($numbImg);

//      echo "docs - ".$arrDoc."<br>images - ".$arrImg;
      // ��������� ���������� � ��������� �������� � ���������
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
    ����������: ������� SiteMap ���������� ����������� ����� �����
    ���������: $auth (I) - ������������� ������ ��� ������. ��������
               $mode (I) - ����� ����������� ������� ���������� $mode TreeMenu
    ��������: ����� ��������� ����
  */

  function SiteMap($auth = 0, $mode = 1, $first = 0, $input = 'chbox')
  {
    global $cPath, $cID, $pages_array, $moved_id, $sort_id, $sort_name;

    $cPath = !empty($cPath)?$cPath:0; $cnt = 0;

    if(!empty($auth))
    {
      $menu = TreeMenu($auth, $first, ($mode!=6?$mode:4), 0);  // ��������� ����� �����
?>                    <div id="dataTable">
                       <table border=0 width=100% cellspacing=0 cellpadding=0>
                        <tr>
                          <th align=left>&nbsp;
                          <th align=left>��������</th>
                          <th align=left>�����</th>
                          <th align=center>�����������</th>
                          <th align=center>��������</th>
                        </tr>
<?
      for($i=0; $i<count($menu); $i++)
        if(isset($menu[$i]["id"]))
          switch($mode)                           // ����� ����������� ��������
          {
            case 6:                               // �������� ����. ������ ���.
                if ($i == 0)
                {
?>
    <tr class="bold_line"><td>��������</td><td>�������� ��������</td><td>������ ����������</td><td>���������� � ������� ����</td></tr>
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

            case 4:                               // ����������� � ���������.
              if ($menu[$i]['parent'] == $cPath)
              {
                if (empty($cID)) $cID = $menu[$i]["id"];

                  $cnt++;

                  echo '<tr onmouseover="rowOverEffect(this, '.($cnt%2).')" onmouseout="rowOutEffect(this, '.($cnt%2).')" class="dataTableRow'.($cnt%2).'"'.'>'."\n";
?>
                          <td align="center">
                            <div><a href="?action=up_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="�����"><img src="images/icons/up.gif" height="6" width="8" border=0 alt="�����"></a></div>
                            <div style="padding-top:3px;"><a href="?action=down_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="����"><img src="images/icons/down.gif" height="6" width="8" border=0 alt="����"></a></div>
                          </td>
                          <td>
                            <?='<a href="?selected_box=page&action=pages&cPath='.$menu[$i]["id"].'">'.
                               $menu[$i]["name"].'</a>';?>
                          </td>
                          <td align="left"><small><?=(!empty($menu[$i]["url"])?$menu[$i]["url"]:"/index.php?page_id=".$menu[$i]["id"]);?></small></td>
                          <td align="center">
<? if (empty($menu[$i]["publ"])){ ?>
                            <a href="?action=unlock_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>"><img src="images/icon_status_green_light.gif" alt="��������������" title=" �������������� " width="18" border="0" height="18"></a>&nbsp;&nbsp;&nbsp;
                            <img src="images/icon_status_red.gif" alt="����������" title=" ���������� " width="18" border="0" height="18">
<? } else { ?>
                            <img src="images/icon_status_green.gif" alt="��������" title=" �������� " width="18" border="0" height="18">&nbsp;&nbsp;&nbsp;
                            <a href="?action=lock_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>"><img src="images/icon_status_red_light.gif" alt="������� ����������" title=" ������� ���������� " width="18" border="0" height="18"></a>
<? } ?>
                          </td>
                          <td align="center">
<? if (!empty($moved_id) and ($moved_id==$menu[$i]['id'])) { ?>
                            <form action="?action=pages" method="get" style="display:inline;">
                              <input type=hidden name="action" value="move_page">
                              <input type=hidden name="page_id" value="<?=$moved_id;?>">
                              <select name="cPath" onChange="if(confirm('�� ������������� ������ ����������� ��������?')) this.form.submit();">
                                <option value=0>������</option>
                                <?=CreateSelOption($sort_id, $sort_name, !empty($cPath)?$cPath:0);?>
                              </select>
                            </form>
<? } else { ?>
                            <a href="?action=edit_page&page_id=<?=$menu[$i]['id'];?>" title="������������"><img src="images/icons/ico_edit.gif" height="22" width="22" border=0 alt="�������������"></a>&nbsp;&nbsp;&nbsp;
                            <a href="?selected_box=page&action=pages&cPath=<?=$cPath;?>&moved_id=<?=$menu[$i]['id'];?>" title="�����������"><img src="images/icons/move.gif" height="22" width="43" border=0 alt="�����������"></a>&nbsp;&nbsp;&nbsp;
<?  if (empty($pages_array[$menu[$i]["id"]])) { ?>
                            <a href="?action=del_page&cPath=<?=$cPath;?>&page_id=<?=$menu[$i]['id'];?>" title="�������" onClick="if(confirm('�� ������������� ������ �������?')) return true; else return false;"><img src="images/icons/ico_del.gif" height="22" width="22" border=0 alt="�������"></a>
<?  } else { ?>
                            <img src="images/icons/b_cancel.gif" height="22" width="22" border=0 alt="������ ������� - ������� ��������� ��������" title="������ ������� - ������� ��������� ��������">
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
                if($i == 0)                       // ���������� �����������
                {                                 // ������ ��� ������� �����
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
    ����������: ������� GetTagNumb ��������� ����� ����������� � ���������� ����
    ���������: $text      (I) - ����� � ������� �������������� �����
               $begin_tag (I) - ����������� ���
               $end_tag   (I) - ����������� ���
    ��������: ������� ���������� ������ �������� ������������ � ������ $text,
              ����������� ����� ������ $begin_tag � $end_tag
  */

  function GetTagNumb($text = '', $begin_tag = '', $end_tag = ']')
  {
    if(!empty($text) and !empty($begin_tag) and !empty($end_tag))
    {
      $res = array();                     // �������������� ������
      $tok = explode($begin_tag, $text);  // ��������� ������� ���������

      // ���� �� ���� ���������� c ���������� �������� �� ������������ ����
      for($i=1; $i<count($tok); $i++)
        $res[] = substr($tok[$i], 0, strpos($tok[$i], $end_tag));

      return $res;                        // ����������� ����������
    }
  }


  /*
    ����������: ������� HTMLAddDoc - ����� ��� ������������ ������
    ��������: ���������� �������� ��� ���������� ������
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
    ����������: ������� HTMLAddImg - ����� ��� ������������ �����������
    ��������: ���������� �������� ��� ���������� �����������
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
    ����������: ������� AttachedFiles - �������� ��������� ������������� �����
    ���������: $arr - ������ ������������ ������
               $name - ������� ������ �������� ��������
               $begin_tag - ����������� ��� (��� �����������)
               $end_tag - ����������� ��� (��� �����������)
    ��������: ���������� ��������� �������� �� ������� $arr. ����������� ������.
              ��� ����������� ��� + �������� �� ������� + ����������� ���.
  */

  function AttachedFiles($arr='', $name, $begin_tag = '', $end_tag = ']')
  {
    if(!empty($arr))                      // ���� ��������� ��������
      foreach($arr as $key => $value)     // � ������� �����
      {                                   // ������� ���������� �� �������
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
    ����������: ������� CreateSelOption - ������ ��-�� ����������� ������
    ���������: $id   (I) - �������������� ������� (�������� value)
               $name (I) - �������� ������� (����� ������������)
    ��������: ��������� ��-�� ����������� ������
  */

  function CreateSelOption($id, $name, $def=-1)
  {
    foreach($id as $key => $value)
      print "<option value='".$value."'".(($def==$value)?" selected":"")." class=tbl_txt>".
            stripslashes($name[$key])."</option>";
  }

  // ������ �������� option, ������� � nFirst � ���������� nLast
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
    ����������: ������� PageMove - ���������� ��������
    ���������: $auth      (I) - ������������� ������ ��� ������. ��������
               $id        (I) - ������������� ��������
               $direction (I) - ����������� ����������� ��������
                                up   - ������
                                down - ����
    ��������: ���������� ��������, � �������� ������ ������ ������ ��� ������ �
              ���� �� ������, � ����������� $direction
  */

  function PageMove($auth = null, $id = 0, $direction = '')
  {
    if(!empty($auth) and !empty($id) and!empty($direction))
    {
      $sql = "select id, id_parent, sort_index from ".TBL_PREF."pages where id=".$id;
      list($id1, $parent, $s_ind) = $auth->QueryExecute($sql, array(0, 1, 2));

      // ���� ���������� ������������ �������� � ��������������� $id
      if(!empty($id1[0]) and !empty($s_ind[0]))
        switch($direction)                // ����������� ����������� �����������
        {
          case "up":                      // ����������� �������� ������
              $sql = "select id, sort_index from ".TBL_PREF."pages
                      where id_parent=$parent[0] and sort_index<$s_ind[0]
                      ORDER BY sort_index DESC";
              list($id2, $sort_index2) = $auth->QueryExecute($sql, array(0,1));

              if(!empty($id2[0]))         // ���� ���� ���. ���� ������������
              {                           // �������� �� ������� ����������
                $sql = "update ".TBL_PREF."pages set sort_index=";
                $auth->QueryExecute($sql."$sort_index2[0] where id=$id1[0]");
                $auth->QueryExecute($sql."$s_ind[0]   where id=$id2[0]");
              }
              break;

          case "down":                    // ����������� �������� ����
              $sql = "select id, sort_index from ".TBL_PREF."pages
                      where id_parent=$parent[0] and sort_index>$s_ind[0]
                      ORDER BY sort_index";
              list($id2, $sort_index2) = $auth->QueryExecute($sql, array(0,1));

              if(!empty($id2[0]))         // ���� ���� ���. ���� ������������
              {                           // �������� �� ������� ����������
                $sql = "update ".TBL_PREF."pages set sort_index=";
                $auth->QueryExecute($sql."$sort_index2[0] where id=$id1[0]");
                $auth->QueryExecute($sql."$s_ind[0]   where id=$id2[0]");
              }
              break;
        }
    }
  }


  /*
    ����������: ������� find - ������������ ����� ���������� � �������� � �� ���������
    ���������: $auth    (I) - ������������� ������ ��� ������. ��������
               $words   (I) - ���� ������
               $place   (I) - �����, ��� ���������� ������
               $arr_res (I) - ������, ������������, ��� ����� �������� �������.
                              0 - �������������
                              1 - ��������
                              2 - �������� �����
                              3 - ������ �����
                              4 - ����
    ����������: ������ �� ��������������� � ������������
    ��������: ����������� ����� $words � $place
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
    ����������: ������� setBoldText - �������� ��������� ����� � ������
    ���������: $txt    (I) - �����
               $a_word (I) - ������ ���� ��� ������ � ������
               $color  (I) - ���� ������, �. �����. �������� ��������� �����
               $bg     (I) - ���� ���� ������, �. �����. �������� ��������� �����
    ����������: ����� $txt � ����������� � �� ������� �� ������� $a_word
    ��������: ��������� ������ <b></b> � ������ $txt ��� ����� ��
              ������� $a_word � ���������� ���� ��������� �����
  */
  function setBoldText($txt = '', $a_word = null, $color = "black", $bg = "white")
  {
    if(!empty($txt) and !empty($a_word[0]))
      foreach($a_word as $key => $value)  // ���� �� ������ �������� �����
        if(!empty($value))                // ��� ����, ����� �������� �����
        {                                 // �������� ������
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
    $page_count = ceil($cnt/$gallery_limit); // ���-�� ���. �������� (�����)
    $first_page = (($limit_begin-$page_gallery) > 0) ?
                   ($limit_begin-$page_gallery) : 0;
    $last_page  = (($limit_begin+$page_gallery) < ($page_count-1)) ?
                   ($limit_begin+$page_gallery) : ($page_count-1);

    if($cnt > $gallery_limit)                // ���� �� ����������
    {                                           // �� ���� ����
      /*
      $html .= "<nobr>";
      if($limit_begin!=0)
        $html .= "<a href='".$href.($limit_begin-1)."'>"."&#171;-</a> ����������";
      print " ";
      if($limit_begin!=($page_count-1))
        $html .= "��������� <a href='".$href.($limit_begin+1)."'>"."-&#187;</a>";
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

    $page_count = ceil($id_cnt/$gallery_limit); // ���-�� ���. �������� (�����)
    $first_page = (($limit_begin-$page_gallery) > 0) ?
                   ($limit_begin-$page_gallery) : 0;
    $last_page  = (($limit_begin+$page_gallery) < ($page_count-1)) ?
                   ($limit_begin+$page_gallery) : ($page_count-1);

    if($id_cnt > $gallery_limit)                // ���� �� ����������
    {                                           // �� ���� ����
      print "<nobr>";
      if($limit_begin!=0)
        print "<a href='".$href.($limit_begin-1)."'>"."&#171;-</a> ����������";
      print " ";
      if($limit_begin!=($page_count-1))
        print "��������� <a href='".$href.($limit_begin+1)."'>"."-&#187;</a>";
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
    ����������: ������� FixedLineFromArr - ������� ����� � ����� �� ������ �������
    ���������: $arr       (I) - ������ ��������� ��������
               $width     (I) - ������������ ������ ������
               $separator (I) - ����������� � �������� ������ ����� ����������
                                ��������� ������� $arr
    ��������: ���������� ��������� ������ ����������� �� ������ ��������
              ���������� � ������� $arr, ����� ������� ����� ������ �����������
              $separator. ����� �������� ������ �� ����� ��������� $width.
  */

  function FixedLineFromArr($btag, $body, $etag, $width = 0, $color = "white")
  {
    $res = "";  $out = "";

    if(!empty($body) and !empty($width))
      if(count($body) > 0)                 // ���� $arr - ������
        foreach($body as $key => $value)
          if(empty($res))                 // ���� ������ �������
          {
            if(strlen($value) < $width)
            {
              $out = $btag[$key].$value.$etag[$key];
              $res = $value;
            }
          }
          else                            // ���� � $res ��� ���� ��������
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


/* $param=1 - 3 ������� � �����
   $param=2 - ��� ������� �������
   $param=3 - ���������� �������
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

/* $param=1 - ������ ������������ � ��������� ��������
   $param=2 - ���������� ��������� �������
   $param=3 - �������� ��������
   $case - ��� ���������
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
  $param=1 ������� �������
  $param=2 ������� ���� ��������
  $param=3 ���� ��������
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



?>