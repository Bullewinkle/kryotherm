<?
  $menu = TestTreeMenu(0);
  for($i=0; $i<count($menu); $i++)
    if(isset($menu[$i]["id"]))
    {
      if(empty($menu[$i]["url"]))
        print "<li class='menu-dop menu".$menu[$i]["level"]."'><a href='".URL."index.php?page_id=".$menu[$i]["id"]."' ".(($_REQUEST['page_id']==$menu[$i]["id"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a></li>";
      else
        print "<li class='menu-dop menu".$menu[$i]["level"]."' ><a href='".$menu[$i]["url"]."' ".(($_SERVER['REQUEST_URI']==$menu[$i]["url"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a></li>";
    }
?>