<?
  // задай id страницы, с которого строить меню
  $start = '95';
  //$start = 0;

  // если не id то URI
  //$limit = (!empty($_REQUEST['page_id'])?$_REQUEST['page_id']:$_SERVER['REQUEST_URI']);

  $limit = $page_id;
  $menu = TestTreeMenu($start,$limit,2);

  for($i=0; $i<count($menu); $i++)
    if(isset($menu[$i]["id"]))
    {
      if(empty($menu[$i]["url"]))
        print "<span class='menu".$menu[$i]["level"]."'><a href='".URL."index.php?page_id=".$menu[$i]["id"]."' ".(($_REQUEST['page_id']==$menu[$i]["id"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a></span>";
      else
        print "<span class='menu".$menu[$i]["level"]."' ><a href='".$menu[$i]["url"]."' ".(($_SERVER['REQUEST_URI']==$menu[$i]["url"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a></span>";
    }
?>