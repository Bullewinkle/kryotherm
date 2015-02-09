<?
//  $id = $auth->QueryExecute("select id from ".TBL_PREF."pages where id=1", 0);
  $id[0] = 1;
  list($txt, $keyw, $desc, $title) = PageView($auth, '', '', $_SERVER['REQUEST_URI']);

?>