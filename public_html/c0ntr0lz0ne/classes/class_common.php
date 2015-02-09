<?
  class Common{

          function __construct($auth){ $this->auth = $auth; }

          function locker()
          {
              $a = 'lock';
          }

          function redirect($href,$timeout=0){

                  print "<script language='Javascript'> setTimeout(\"document.location.href='".$href."'\",".$timeout."); </script>";
          }

          function GetNewsId($headline){

                  $id = $this->auth->QueryExecute("SELECT id FROM ".TBL_PREF."news WHERE headline='".addslashes(stripslashes($headline))."'",0);

                  return $id;
          }

          function prepareString($string){

                  return addslashes(stripslashes($string));
          }
  }
?>