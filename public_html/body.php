<?
  $show_right = null;

  if (!empty($_REQUEST['idCat']) ||
      strpos($_SERVER['REQUEST_URI'],'cart.php') ||
      !empty($_REQUEST['exec_order']) ||
      strpos($_SERVER['REQUEST_URI'],'compare.php') ||
      strpos($_SERVER['REQUEST_URI'],'filter_result.php')
      )

      $show_right = 1;

  if(!empty($_REQUEST['newsId']))
  {
      if($_REQUEST['newsId'] == 'all') $r = getNews(3);   // архив
      else                             $r = getNews(1); // конкретная новость

          $txt = html_entity_decode($r['content']);
          $desc = $r['description'];
          $keyw = $r['keywords'];
          $title = $r['title'];

      $txt .= getGallery($auth,1,'news');  // список прикрепленных к материалу галерей

  }
  elseif(!empty($_REQUEST['galleryId'])){

     $r = getGallery($auth,2);
     $txt = html_entity_decode($r['content']);
     $keyw = $r['keyw'];
     $desc = $r['desc'];
     $title = $r['title'];

  }
  elseif(!empty($_REQUEST['imageId'])){

     $r = getGallery($auth,3);

     $txt = html_entity_decode($r['content']);
     $keyw = $r['keyw'];
     $desc = $r['desc'];
     $title = $r['title'];

  }
  elseif($_SERVER['REQUEST_URI'] == '/gallery.php'){

          $txt .= getGallery($auth,4,'all');
  }
  elseif(!empty($_REQUEST['idProd'])){

      $r = getCatalog($auth,3,'',$_REQUEST['idCat'],$_REQUEST['idProd']);
      $txt = html_entity_decode($r['content']);
      $keyw = $r['keyw'];
      $desc = $r['desc'];
      $title = $r['title'];

      $txt .= getGallery($auth,1,'product');
  }
  elseif(($_SERVER['REQUEST_URI'] == '/' or isset($_REQUEST['idManuf']) or isset($_REQUEST['idCat'])) and empty($_REQUEST['idProd'])){

     
     if ($_SERVER['REQUEST_URI'] == '/')

         $r = getCatalog($auth,4);
     else
         $r = getCatalog($auth,5, '', '', '', $filter);

     $txt = html_entity_decode($r['content']);
     $keyw = $r['keyw'];
     $desc = $r['desc'];
     $title = $r['title'];
  }
  elseif (strpos($_SERVER['REQUEST_URI'],'cart.php') || !empty($_REQUEST['exec_order']))
  {
      if (!empty($_REQUEST['clear_cart']))
          $_SESSION['user_cart'] = array();

      if ($_REQUEST['exec_order'] == 'form')
      {
          $txt .= get_order_form($_SESSION['user_cart'],(!empty($_REQUEST['customer'])?$_REQUEST['customer']:1));
      }
      elseif ($_REQUEST['exec_order'] == 'send')
      {
          $txt .= exec_order_form($mail, $_POST, $_SESSION['user_cart']);
      }
      else
          $txt .= get_user_cart($_SESSION['user_cart']);

  }elseif (strpos($_SERVER['REQUEST_URI'],'filter_result.php')){

      include(CATALOG_SCRIPT_DIR.'filter_result.php');

  }elseif (strpos($_SERVER['REQUEST_URI'],'compare.php')){

      include(CATALOG_SCRIPT_DIR.'compare.php');

  }elseif (strpos($_SERVER['REQUEST_URI'],'search.php')){

      include('searching.php');

  }else{

    list($txt, $keyw, $desc, $title) =
      PageView($auth, intval( $_REQUEST['page_id']),
               ((!empty($_REQUEST['word']))? mysql_real_escape_string($_REQUEST['word']):""), mysql_real_escape_string($_SERVER['REQUEST_URI']) );

    $txt .= getGallery($auth,1,'pages');  // список прикрепленных к материалу галерей

    $copyright = "&copy; Сайт создан в <br>kreazone.ru '2009";

  }
?>