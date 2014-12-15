<?
  $menu = TestTreeMenu(0,1,2);
  for($i=0; $i<count($menu); $i++)
    if(isset($menu[$i]["id"]))
    {
      if(empty($menu[$i]["url"]))
        print "<span> <a href='".URL."index.php?page_id=".$menu[$i]["id"]."' ".(($_REQUEST['page_id']==$menu[$i]["id"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a> </span>";
      else
        print "<span> <a href='".$menu[$i]["url"]."' ".(($_SERVER['REQUEST_URI']==$menu[$i]["url"])?'class="selected"':'')." title='".$menu[$i]["name"]."'>".$menu[$i]["name"]."</a> </span>";
    }


?>