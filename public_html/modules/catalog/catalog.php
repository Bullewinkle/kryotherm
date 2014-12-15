<?
  function getAllCatalog($auth)
  {
      $idManuf     = $_REQUEST['idManuf'];
      $idCat       = (!empty($_REQUEST['idCat'])?$_REQUEST['idCat']:0);
      $limit_begin = $_REQUEST['limit_begin'];
      $per_page    = (!empty($_REQUEST['per_page'])?$_REQUEST['per_page']:6);
      $sort        = (!empty($_REQUEST['sort'])?$_REQUEST['sort']:1);
      $first_pos   = $limit_begin*$per_page;

      $href = 'index.php?idManuf='.$idManuf.'&idCat='.$idCat.'&per_page='.$per_page.'&limit_begin=';

      $sort_arr = array(1 => 'p.name asc',  2 => 'p.name desc',
                        3 => 'p.price asc', 4 => 'p.price desc');

      $catIdArr = categoriesTree(null,$idCat);
      if(is_array($catIdArr)) foreach($catIdArr as $k=>$v) $idStr .= ','.$v['id'];

      list($idCount)=
      $auth->QueryExecute("SELECT IF(p.id_manufacturers,IF(m.published,p.id,0),p.id) as idList
                           FROM ".TBL_PREF."catalog_products p

                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                           LEFT JOIN ".TBL_PREF."catalog_categories c ON p2c.id_category=c.id
                           LEFT JOIN ".TBL_PREF."catalog_manufacturers m ON p.id_manufacturers=m.id

                           WHERE p.published=1  AND
                                 c.published='1'
                                 ".(!empty($idManuf)?" AND p.id_manufacturers=".$idManuf." AND m.published='1'":"")." AND
                                 p2c.id_category IN (".$idCat.$idStr.")
                           ",array(0,1));

      if (is_array($idCount))
         foreach($idCount as $k=>$v) if (!empty($v)) $Count[] = $v;

      list($id,$id_manuf,$price,$name,$text,$created,$modified,$image,$manufacturers,$id_Cat,$manufStatus)=
      $auth->QueryExecute("SELECT p.*,m.name manufacturers,c.id, IF(p.id_manufacturers,IF(m.published,1,0),1) as manufStatus
                           FROM ".TBL_PREF."catalog_products p

                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                           LEFT JOIN ".TBL_PREF."catalog_categories c ON p2c.id_category=c.id
                           LEFT JOIN ".TBL_PREF."catalog_manufacturers m ON p.id_manufacturers=m.id
                           WHERE p.published=1 AND c.published='1'

                           ".(!empty($idManuf)?"AND p.id_manufacturers=".$idManuf."":"")."
                           AND p2c.id_category IN (".$idCat.$idStr.")
                           ORDER BY manufStatus DESC,".$sort_arr[$sort]."
                           LIMIT ".$first_pos.",".$per_page."
                           ",array(0,1,2,3,4,5,6,9,14,15,16));

      $html .= '<div class="catalog_wrapper">';
      /*
      $html .= '<div class="catalog_header">
               '.get_filter_set($auth, $idCat, $idManuf, $idStr, $sort, $per_page).'
                <div class="pagesTab">
               '.get_pages_tab($href, $first_pos, $Count, $limit_begin, $per_page).'
                </div>';
      */
      $count = 0;

      if (is_array($id)) foreach ($id as $k=>$v)
      {
        //if($manufStatus[$k] == 1)
       // {
            $count = $count+1;
            $html .= '<div class="catalog_product_item">
                       <div class="catalog_product_img">
                       '.(!empty($image[$k])?'<a href="index.php?idProd='.$v.'"><img src="'.A_PRODUCT_IMG_URL.'small_'.$image[$k].'" border="0" alt="'.$name[$k].'"></a>':'').'
                       </div>
                       <p class="catalog_product_header"><a href="index.php?idProd='.$v.'&idCat='.$id_Cat[$k].'">'.$manufacturers[$k].'  '.$name[$k].'</a></p>
                       <p class="catalog_product_price">Цена:'.(!empty($price[$k])?$price[$k]:' &mdash;').'</p>
                       </div>';

            if(($count)%3 == 0) $html .= '<br class="clearfloat" />';
       // }
      }
      else $html .= '<p style="text-align: center">Нет товаров</p>';
      /*
      $html .= '<div class="catalog_footer">
                <div class="pagesTab">
               '.get_pages_tab($href, $first_pos, $Count, $limit_begin, $per_page).'
                </div>
                </div>';
     */
      if (!empty($idManuf))
      {
          list($name,$descr,$title,$keyw) =
          $auth->QueryExecute("SELECT name,description,title,keywords FROM ".TBL_PREF."catalog_manufacturers WHERE id='".$idManuf."'",array(0,1,2,3));

          $title = (!empty($title[0])?$title[0]:$name[0]);
          $description = (!empty($descr[0])?$descr[0]:$name[0]);
          $keywords = (!empty($keyw[0])?$keyw[0]:$name[0]);
      }

      if(!empty($idCat))
      {
          list($name,$descr,$title,$keyw) =
          $auth->QueryExecute("SELECT name,description,title,keywords FROM ".TBL_PREF."catalog_categories WHERE id='".$idCat."'",array(0,1,2,3));

          if (!empty($idManuf))
          {
              $title = $manufacturers[0].'. '.$name[0].'.';
              $description = $manufacturers[0].'. '.$name[0].'.';
              $keywords = $manufacturers[0].','.$name[0];
          }
          else
          {
              $title = (!empty($title[0])?$title[0]:$name[0]);
              $description = (!empty($descr[0])?$descr[0]:$name[0]);
              $keywords = (!empty($keyw[0])?$keyw[0]:$name[0]);
          }
      }
      $array = array('title'=>$title, 'desc'=>$description, 'keyw'=>$keywords, 'content'=>htmlentities($html));

  return $array;
  }

  function get_pass($categories, $idCat, $default = null, $result=null)
  {
      if (is_array($categories)) foreach ($categories as $k => $v)
      {
          if ($v['id'] == $idCat)
          {
              $result = ' > <a href="/index.php?idCat='.$v['id'].'" '.(($v['id'] == $default)?"class='active'":"").'>'.$v['name'].'</a>'.$result;
              unset($categories[$k]);
              $result = get_pass($categories, $v['id_parent'], $default, $result);
          }
      }
  return $result;
  }

  function getInnerView($auth, $param, $filter_obj = '')
  {
      $idCat       = (!empty($_REQUEST['idCat'])?$_REQUEST['idCat']:0);
      $limit_begin = (!empty($_REQUEST['limit_begin'])?$_REQUEST['limit_begin']:0);
      $per_page    = (!empty($_REQUEST['per_page'])?$_REQUEST['per_page']:20);
      $sort        = (!empty($_REQUEST['sort'])?$_REQUEST['sort']:1);
      $first_pos   = $limit_begin*$per_page;
      $order = (!empty($_REQUEST['order'])?$_REQUEST['order']:'name_asc');

      $title = "Криотерм";
      $keywords = "Термоэлектрические модули, термоэлектрические сборки";
      $description = "Kryotherm";

      $href = 'index.php?&idCat='.$idCat.'&order='.$order;

      $categories = categoriesTree(null);
      $catIdArr = categoriesTree(null, $idCat);
      $catId = getCategoriesVsProducts($auth);
      $pass = get_pass($categories, $idCat, $idCat);

      if (is_array($catIdArr))
          foreach ($catIdArr as $k=>$v)
              if ($catId[$v['id']]) $idStr .= ','.$v['id'];

      $order_set = '';

      if (!empty($order))
      {
          $sort_type = strrchr($order, '_');

          if ($sort_type == '_asc') $sort = 'ASC';
          elseif ($sort_type == '_desc') $sort = 'DESC';

          //$field = str_replace($sort_type, '', $order);

          $order_set = ' ORDER BY id_parent, name '.$sort;

           if (strpos($order, '_desc'))
           {
               $up = 'up_passive.gif';
               $down = 'down_active.gif';
           }
           elseif (strpos($order, '_asc'))
           {
               $up = 'up_active.gif';
               $down = 'down_passive.gif';
           }
      }

      $sql = "SELECT * FROM ".TBL_PREF."catalog_categories
              WHERE published = '1' AND
                    id IN (".$idCat.$idStr.")".$order_set;

      //print $sql;

      $q = $auth->db->Execute($sql); $r = mysql_fetch_array($q);


      $html .= '<div style="height: 54px"><a href="/">В каталог</a>'.$pass.'</div>
                <h1>'.$r['name'].'</h1>
                <!--    <img src="'.A_CATALOG_IMG_URL.$r['image'].'">  -->
               ';

      if ($r['id_parent'] == 0)
      {
          $html .=
          '<div class="subtitle pad20">
           <div class="round">
           <b>'.$r['name'].'</b>
           </div>
           </div>
          <table width="100%">
          <tr>
              <td width="200" valign="top" class="kart">
          '.(!empty($r['image'])?'
                  <!--<a href="/index.php?idCat='.$r['id'].'">-->
                  <img class="kartinka" src="'.A_CATALOG_IMG_URL.'medium_'.$r['image'].'">
                  <!--</a>-->':'').'
              </td>
              <td valign="top" class="spisok">
                  '.$r['story_text'].'
                  '.get_subcategories($auth,$r['id'],10).'
              </td>
          </tr>
          </table>';

          /*
          $html .= '<p>'.viewPages(count(explode(',',$idStr))-1, $limit_begin, $per_page, 4, $href.'&limit_begin=').'</p>
                    <table width="100%" border="0" cellpadding="0" cellspacing="1" class="items" >
                    <tr class="grey">
                        <th class="tal">
                            <a href="/index.php?idCat='.$idCat.'&limit_begin='.$limit_begin.'&order=name_asc">
                                <img src="img/'.$up.'" border="0" width="8"style="margin: 0"></a>

                            <a href="/index.php?idCat='.$idCat.'&limit_begin='.$limit_begin.'&order=name_desc">
                                <img src="img/'.$down.'" border="0" width="8" style="margin: 0"></a>
                            Наименование
                        </th>
                        <th>Скачать</th>
                    </tr>
                   ';
          $count = $items = 0;

          while ($r = mysql_fetch_array($q))
          {
              $count++;

              if ($count > $first_pos)
              {
                  $items++; if ($items <= $per_page)

                      $html .=
                      '<tr '.(($items%2 == 0)?"class='grey'":"").'>
                           <td><a href="index.php?idCat='.$r['id'].'">'.$r['name'].'</a></td>
                           <td class="tac" width="10%">
                      '.(!empty($r['pdf_file'])?'<a href="'.A_CATALOG_PDF_URL.$r['pdf_file'].'" target="_blanck"><img src="/img/pdf.gif" /></a>':'').'
                           </td>
                       </tr>
                      ';
                  else break;

              }
          }

          $html .= '</table>';
          */
      }
      else
      {
          $where_set = $order_set = $sort = $sort_type = $up = $down = '';
          $filter = '';

          if (!empty($order))
          {
              $sort_type = strrchr($order, '_');

              if ($sort_type == '_asc') $sort = 'ASC';
              elseif ($sort_type == '_desc') $sort = 'DESC';

              $field = str_replace($sort_type, '', $order);

              $order_set = ' ORDER BY p.'.$field.' '.$sort;
          }

          $sql = "SELECT p.*, (1*p.price), p2c.id_category category_id, c.pdf_file, c.story_text cat_text

                  FROM ".TBL_PREF."catalog_products p

                  LEFT JOIN  ".TBL_PREF."catalog_p2c p2c ON p.id = p2c.id_product
                  LEFT JOIN  ".TBL_PREF."catalog_categories c ON p2c.id_category = c.id

                  WHERE p2c.id_category = '".$idCat."' AND p.published = '1'
                 ".$order_set;

          $q = $auth->db->Execute($sql);
          $count = $auth->db->NumRows();

          $r = mysql_fetch_array($q);

         $help_ids = $filter_obj->get_help_id();

/*
          $html .=
          (!empty($r['pdf_file'])
           ?'<p style="clear: both; text-align: right; margin-top: -58px;"><a href="http://'.$_SERVER['SERVER_NAME'].'/pdf_docs/'.$r['pdf_file'].'" target="_blanck" title="Скачать спецификацию">
                <img src="/img/pdf.gif" border="0"></a></p>'
           :'');
*/

          $html .= $r['cat_text'];

          $html .=
          (!empty($r['pdf_file'])
           ?'<p style="clear: both;"><a href="http://'.$_SERVER['SERVER_NAME'].'/pdf_docs/'.$r['pdf_file'].'" target="_blank" style="background:url(/img/small_pdf.gif) no-repeat 0px 0px; padding-left:25px; height:20px; line-height:18px; display:block;">Скачать спецификацию</a></p>'
           :'');

          if ($count > 0)
              mysql_data_seek($q,0);

          $header = products_header_data();

          $html .= viewPages($count, $limit_begin, $per_page, 4, $href.'&limit_begin=').
                   '<div id="help_holder"></div>
                    <table width="100%" border="0" cellpadding="0" cellspacing="1" class="items" >
                    <tr class="grey">';

          $html .= get_products_header($header, $order, '/index.php?idCat='.$idCat.'&limit_begin='.$limit_begin.'&', '',$help_ids);

          $count = $items = 0;
          while ($r = mysql_fetch_array($q))
          {
              $count++;

              if ($count >= $first_pos && $count < ($first_pos+$per_page))
              {
                  $items++; //if ($items <= $per_page)

                  $html .= '<tr '.(($items%2 == 0)?"class='grey'":"").'>
                           '.get_product_content($r).'
                            </tr>';
              }
          }

          $html .= '</table>';

          $html = $html;
      }

  return $array = array('title'=>$title, 'desc'=>$description, 'keyw'=>$keywords, 'content'=>htmlentities($html));
  }

  function get_product_content($r, $mode = '')
  {
  return '<td width="80">'.$r['name'].'</td>
          <td align="center">'.$r['I_max'].'</td>
          <td align="center">'.$r['Q_max'].'</td>
          <td>'.$r['U'].'</td>
          <td align="center">'.$r['R'].'</td>
          <td>'.$r['A'].'</td>
          <td>'.$r['B'].'</td>
          <td>'.$r['C'].'</td>
          <td>'.$r['D'].'</td>
          <td>'.$r['H'].'</td>
          <td>'.$r['Drus'].'</td>
          <td>'.$r['di'].'</td>
          <td>'.$r['sealing_type'].'</td>
          <td align="center">'.round($r['HT'],0).'</td>
          <td align="center">'.$r['quant'].'</td>
          <td align="center">'.$r['price'].'</td>
          <td>'.buy_form($href, $r['id'], $r['price'], $r['name'],
                         $r['category_id'], $r['image'],
                         $r['I_max'], $r['Q_max'], $r['U'], $r['R'], $r['A'],
                         $r['B'], $r['C'], $r['D'], $r['H'], $r['Drus'], $r['di'],
                         $r['sealing_type'], $r['HT'], $r['pdf_file'], $r['quant'], $mode).'
          </td>';
  }

/*  Заголовок таблицы продукции
*/
  function products_header_data()
  {
  return array('name'  => 'Название<br />',
               'I_max' => 'I<sub>max</sub> (A)',
               'Q_max' => 'Q<sub>max</sub> (Вт)',
               'U'     => 'U<br />(В)',
               'R'     => 'R (Ом)',
               'A'     => 'A (мм)',
               'B'     => 'В (мм)',
               'C'     => 'С (мм)',
               'D'     => 'D (мм)',
               'H'     => 'H (мм)',
               'Drus'  => '&Oslash;<br />внеш.',
               'd'     => '&Oslash;<br />внутр.',
               'sealing_type' => '<nobr>Тип герм</nobr><br />',
               'HT'    => 'HT<br />',
               'quant' => 'Кол-во шт.',
               'price' => 'Цена (руб/шт)');
  }

/*  Построение таблицы продукции
*/
   function get_products_header($array, $order, $href, $mode = '', $help_ids = '')
   {
       if (is_array($array)) foreach ($array as $k => $v)
       {
           if (strpos('s'.$order,$k.'_') && strpos($order, '_desc'))
           {
               $up = 'up_passive.gif';
               $down = 'down_active.gif';
           }
           elseif (strpos('s'.$order,$k.'_') && strpos($order, '_asc'))
           {
               $up = 'up_active.gif';
               $down = 'down_passive.gif';
           }
           else
           {
               $up = 'up_passive.gif';
               $down = 'down_passive.gif';
           }

           $html .= '<th>
                    '.($help_ids[$k]
                     ?'<img src="/img/help.gif" onClick="show_help_2(this, '.$help_ids[$k].');"><br />':'').'

                    '.$v.'<br />
                     <nobr position: relative; bottom: 0px;>
                     <a href="'.$href.'order='.$k.'_asc">
                       <img src="img/'.$up.'" border="0" width="9"style="margin: 0"></a>
                     <a href="'.$href.'order='.$k.'_desc">
                       <img src="img/'.$down.'" border="0" width="9" style="margin: 0;"></a>
                     </nobr>
                     </th>';
       }
       $html .= '<th style="font-size: 12px; width: 93px; text-align: left;">
                 <img src="/img/cart.gif" align="left" style="margin: 0;"> &mdash;&nbsp;купить<br style="clear: both;" />
                 <img src="/img/'.(($mode =='compare')?'un':'').'compare.gif" align="left" style="margin: 0;"> &mdash;&nbsp;'.(($mode =='compare')?'удалить':'сравнить').'</th></tr>';
   return $html;
   }


/*  Список страничек
*/
  function get_pages_tab($href, $first_pos, $Count, $limit_begin, $per_page)
  {
  return '<span style="float:left">Показано <b>'.($first_pos+1).'</b> - <b>'.min(count($Count), $first_pos+$per_page).'</b> (из <b>'.count($Count).'</b> товаров)</span>
          <span style="float:right">Страницы: '.viewPages(count($Count), $limit_begin, $per_page, 4, $href).'</span></div>';
  }
/*  Фильтры
*/
  function get_sort_list($sort)
  {
  return '<select name="sort" onchange="selecter.submit()" style="width:110px;">
          <option value="1" '.(($sort==1)?"selected":"").'>имя А-Я</option>
          <option value="2" '.(($sort==2)?"selected":"").'>имя Я-А</option>
          <option value="3" '.(($sort==3)?"selected":"").'>цена 0-9</option>
          <option value="4" '.(($sort==4)?"selected":"").' >цена 9-0</option>
          </select>';
  }
/*  Фильтры постраничного вывода
*/
  function get_perpage_filter($per_page)
  {
  return '<select name="per_page" onchange="selecter.submit()">
          <option value="6" '.(($per_page=="6")?"selected":"").'>по 6 </option>
          <option value="12" '.(($per_page=="12")?"selected":"").'>по 12 </option>
          <option value="24" '.(($per_page=="24")?"selected":"").'>по 24 </option>
          <option value="48" '.(($per_page=="48")?"selected":"").'>по 48 </option>
          </select>';
  }
/*  Набор фильтров
*/
  function get_filter_set($auth, $idCat, $idManuf, $idStr, $sort, $per_page)
  {
  return '<form name="selecter" action="index.php" method="get">
          <div class="catalog_manuf_list">
            <select name="idManuf" onChange="javascript:selecter.submit();" >
              <option value="0" >Производители:</option>'.getManufacturersList($auth,$idManuf,$idCat.$idStr).'
            </select>
          </div>
          <div class="catalog_filter">
            <select name="idCat" onChange="javascript:selecter.submit();" >
              <option value="0" >Категория:</option>'.getCategoriesList($auth,$idCat,$idManuf).'
            </select>
          </div>
          <div class="sort_filter">'.get_sort_list($sort).'</div>
          <div class="per_page_filter">'.get_perpage_filter($per_page).'</div>
          </form>';
  }

  function getProduct($auth,$idCat,$idProd)
  {
      list($id,$price,$name,$text,$image,$title,$description,$keywords,$nameManuf,$idManuf,$nameCat)=
      $auth->QueryExecute("SELECT p.*,m.name,m.id,c.name
                           FROM ".TBL_PREF."catalog_products p
                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                           LEFT JOIN ".TBL_PREF."catalog_categories c ON p2c.id_category=c.id
                           LEFT JOIN ".TBL_PREF."catalog_manufacturers m ON p.id_manufacturers=m.id
                           WHERE p.id='".$idProd."'",array(0,2,3,4,9,10,11,12,14,15,16));

      $buy_link = null;

      if (strpos($_SERVER['REQUEST_URI'],"&buy="))
          $buy_link = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],"&buy="));
      else
          $buy_link = $_SERVER['REQUEST_URI'];

      $html = '<div class="catalog_product_wrapper">
               <h1>'.$name[0].'</h1>
               <div class="catalog_product_content">
              '.(!empty($image[0])?'<a href="'.A_PRODUCT_IMG_URL.$image[0].'" target="_blanck" title="'.$name[0].'"><img src="'.A_PRODUCT_IMG_URL.'medium_'.$image[0].'" align="left" alt="'.$name[0].'"></a>':'').'
               <p class="catalog_product_price">Цена: '.(!empty($price[0])?$price[0]:'&mdash;').'</p>
               <p>'.buy_form($buy_link,$id[0], $price[0], $name[0], $idCat, $image[0]).'</p>
               <p class="catalog_product_manufacturer">Производитель: '.$nameManuf[0].'</p>
              '.$text[0].'
               </div>
               <div class="catalog_product_bottom_link">
               <a href="index.php?idCat='.$idCat.'">Все товары категории '.$nameCat[0].'</a>
               <a href="index.php?idManuf='.$idManuf[0].'">Все товары производителя '.$nameManuf[0].'</a>
               </div>
               </div>
              ';

      $title = (!empty($title[0])?$title[0]:$name[0].'.'.$nameManuf[0]);
      $description = (!empty($description[0])?$description[0]:$name[0].'.'.$nameManuf[0]);
      $keywords = (!empty($keywords[0])?$keywords[0]:$name[0].','.$nameManuf[0]);

      $array = array('title'=>$title, 'desc'=>$description, 'keyw'=>$keywords, 'content'=>htmlentities($html));

  return $array;
  }

  function getManufacturersList($auth,$def='',$idCat='')
  {
      list($id,$name) =
      $auth->QueryExecute("SELECT m.id,m.name FROM ".TBL_PREF."catalog_manufacturers m
                           LEFT JOIN ".TBL_PREF."catalog_products p ON m.id=p.id_manufacturers
                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                           WHERE m.published=1 AND
                                 p.published=1
                                 ".(!empty($idCat)?" AND p2c.id_category IN (".$idCat.")":"")."
                           GROUP BY m.id ORDER BY m.sort_order",array(0,1));

      if (is_array($id)) foreach($id as $k=>$v)
          $html .= '<option value="'.$v.'" '.(($v == $def)?'selected':'').' >'.$name[$k].'</option>';

  return $html;
  }

  function categoriesTree($tree=null, $parentID = 0, $level='')
  {
      $q = mysql_query("SELECT *
                        FROM ".TBL_PREF."catalog_categories
                        WHERE id_parent = '".$parentID."' AND
                              published='1'
                        GROUP BY id
                        ORDER BY sort_order ");

      $level = $level.'&nbsp; | ';
      while ($row = mysql_fetch_assoc($q))
      {
          $tmp_i = count($tree);
          $tree[] = $row;
          $tree[$tmp_i]['level'] = $level;
          $tree = categoriesTree($tree, $row['id'], $level);
      }

  return $tree;
  }

  function getCategoriesVsProducts($auth,$idManuf='')
  {
      $idArray = null;

      $id = $auth->QueryExecute("SELECT IF(p.id_manufacturers,IF(m.published,c.id,0),c.id) as idList
                                 FROM ".TBL_PREF."catalog_categories c

                                 LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON c.id=p2c.id_category
                                 LEFT JOIN ".TBL_PREF."catalog_products p ON (p2c.id_product=p.id AND p.published='1')
                                 LEFT JOIN ".TBL_PREF."catalog_manufacturers m ON p.id_manufacturers=m.id

                                 WHERE c.published='1' AND
                                       p.published='1'
                                ".(!empty($idManuf)?" AND p.id_manufacturers='".$idManuf."'":"")."
                                ",0);

      /*
      print "SELECT IF(p.id_manufacturers,IF(m.published,c.id,0),c.id) as idList
                                 FROM ".TBL_PREF."catalog_categories c

                                 LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON c.id=p2c.id_category
                                 LEFT JOIN ".TBL_PREF."catalog_products p ON (p2c.id_product=p.id AND p.published='1')
                                 LEFT JOIN ".TBL_PREF."catalog_manufacturers m ON p.id_manufacturers=m.id

                                 WHERE c.published='1' AND
                                       p.published='1'
                                ".(!empty($idManuf)?" AND p.id_manufacturers='".$idManuf."'":"")."
                                <br /><br /><br /><br />";
        */
      if (is_array($id)) foreach($id as $k=>$v) $string .=','.$v;

      $string = substr($string,1);
      $s = getParents($auth,$string);

      $idArray = explode(',',$string.$s);
      $idArray = array_flip($idArray);

      array_walk ($idArray, 'increment');

  return $idArray;
  }

      function increment(&$val, $key)
      {
          $val = $val+1;
      }

  function getParents($auth,$string='',$result='')
  {
      if (!empty($string))
      {
          $id = $auth->QueryExecute("SELECT GROUP_CONCAT(DISTINCT(id_parent))
                                     FROM ".TBL_PREF."catalog_categories
                                     WHERE id IN (".$string.")",0);
          $result .= ','.$id[0];
          $result .= getParents($auth,$id[0],$result);
      }

  return $result;
  }


  function getCategoriesList($auth,$def='',$idManuf='')
  {
      $catTree = categoriesTree();
      $CatId = getCategoriesVsProducts($auth,$idManuf);

      if (is_array($catTree)) foreach ($catTree as $k=>$v)
          if (isset($CatId[$v['id']]))
              $html .= '<option value="'.$v['id'].'" '.(($v['id'] == $def)?'selected':'').' >'.$v['level'].$v['name'].'</option>';

  return $html;
  }

  function catalogMenu($auth, $start,$idCat='')
  {
      $tree = categoriesTree();
      $parents = getParents($auth,$idCat);
      $catVsProd = getCategoriesVsProducts($auth);

      $idArray = explode(',',$parents);
      $idArray = array_flip($idArray);

      if (is_array($tree)) foreach ($tree as $k=>$v)
          if ((isset($idArray[$v['id_parent']]) or $v['id_parent'] == $idCat)  and
              isset($catVsProd[$v['id']]) and $v['id_parent'] == '0')

              $html .= '<li class="menu'.(strlen($v['level'])/9).'">
                        <a '.(($idCat==$v['id'])?"class='selected'":"").'
                           href="index.php?idCat='.$v['id'].'" >'.$v['name'].'
                        </a></li>';

  return $html;
  }

  function get_subcategories($auth,$id_parent, $limit = 5)
  {
      $catIdArr = categoriesTree(null, $id_parent);
      $catId = getCategoriesVsProducts($auth);

      if (is_array($catIdArr))
          foreach ($catIdArr as $k=>$v)
              if ($catId[$v['id']]) $idStr .= ','.$v['id'];

      if (strstr($idStr,',')) $idStr = substr($idStr,1);

      if (!empty($idStr))
      {
      $sql = "SELECT * FROM ".TBL_PREF."catalog_categories
              WHERE published = '1' AND
                    id IN (".$idStr.")
              ORDER BY name

             ";

      $q = $auth->db->Execute($sql);

      $html .= '<br /><br /><form action="index.php" method="get">
                <select name="idCat" onChange="javascript: this.form.submit();" style="width: 100%">
                <option value="0"> Выберите товар: </option>
               ';


      while ($r = mysql_fetch_array($q))

          $html .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';

          $html.= '</select></form>';
      }
  return $html;
  }

/*  Получить инфу на главную страницу
*/
  function getIndexView($auth)
  {
      $html = '<h1>Термоэлектрические модули</h1>';

      $sql = "SELECT * FROM ".TBL_PREF."catalog_categories
              WHERE id_parent='0' ORDER BY sort_order";

      $q = $auth->db->Execute($sql);

      $title = "Криотерм";
      $keywords = "Термоэлектрические модули, термоэлектрические сборки";
      $description = "Kryotherm";

      while ($r = mysql_fetch_array($q))

          $html .=
          '<div class="subtitle pad20">
           <div class="round">
           <b>'.$r['name'].'</b>
           </div>
           </div>
          <table width="100%">
          <tr>
              <td width="200" valign="top" class="kart">
          '.(!empty($r['image'])?'
                  <!--<a href="/index.php?idCat='.$r['id'].'">-->
                  <img class="kartinka" src="'.A_CATALOG_IMG_URL.'medium_'.$r['image'].'">
                  <!--</a>-->':'').'
              </td>
              <td valign="top" class="spisok">
                  '.$r['story_text'].'
                  '.get_subcategories($auth,$r['id'],10).'
              </td>
          </tr>
          </table>
          ';

      $array = array('title'=>$title,
                     'desc'=>$description,
                     'keyw'=>$keywords,
                     'content'=>htmlentities($html));

  return $array;
  }
      /*
      buy_form($href, $r['id'], $r['price'], $r['name'],
                                           $idCat, $r['image'],
                                           $r['I_max'], $r['Q_max'], $r['R'], $r['A'],
                                           $r['B'], $r['C'], $r['D'], $r['H'], $r['Д'], $r['di'],
                                           $r['sealing_type'], $r['HT'],
                                           $prod_quant = null)
                                           */
/*  Формочка для покупок
*/
  function buy_form($action, $prod_id = null, $prod_price = null, $prod_name = null,
                    $idCat = null, $prod_img = null, $I_max = null, $Q_max = null, $U = null,
                    $R = null, $A = null, $B= null, $C = null, $D = null, $H = null, $Drus = null,
                    $di = null, $sealing_type = null, $HT = null, $pdf_file = null,  $total = null, $mode = null,
                    $prod_quant = null)
  {
  return  '<form name="FormName" action="'.$action.'" method="post">
               <input name="cart_prod_quant" type="text" size="1" style="float: left" maxsize="4" value="'.(empty($prod_quant)?"1":$prod_quant).'">
               <input name="cart_prod_id" type="hidden" value="'.$prod_id.'">
               <input name="cart_prod_price" type="hidden" value="'.$prod_price.'">
               <input name="cart_prod_name" type="hidden" value="'.$prod_name.'">
               <input name="cart_cat_id" type="hidden" value="'.$idCat.'">
               <input name="cart_prod_img" type="hidden" value="'.$prod_img.'">
               <input name="cart_prod_I_max" type="hidden" value="'.$I_max.'">
               <input name="cart_prod_Q_max" type="hidden" value="'.$Q_max.'">
               <input name="cart_prod_U" type="hidden" value="'.$U.'">
               <input name="cart_prod_R" type="hidden" value="'.$R.'">
               <input name="cart_prod_A" type="hidden" value="'.$A.'">
               <input name="cart_prod_B" type="hidden" value="'.$B.'">
               <input name="cart_prod_C" type="hidden" value="'.$C.'">
               <input name="cart_prod_D" type="hidden" value="'.$D.'">
               <input name="cart_prod_H" type="hidden" value="'.$H.'">
               <input name="cart_prod_Drus" type="hidden" value="'.$Drus.'">
               <input name="cart_prod_di" type="hidden" value="'.$di.'">
               <input name="cart_prod_sealing_type" type="hidden" value="'.$sealing_type.'">
               <input name="cart_prod_HT" type="hidden" value="'.$HT.'">
               <input name="cart_prod_pdf_file" type="hidden" value="'.$pdf_file.'">
               <input name="cart_prod_total" type="hidden" value="'.$total.'">


               <input name="buy" type="hidden" value="">
               <input name="compare" type="hidden" value="">

          '.(empty($prod_quant)
           ?'<input type="image" src="/img/cart.gif" title="Положить в корзину" onClick="javascript: this.form.buy.value = 1; this.form.submit();" align="left">'
           :'<input type="image" src="/img/refresh.gif" title="Пересчитать" onClick="javascript: this.form.buy.value = 1; this.form.submit();" align="left">').'



          '.(empty($prod_quant)
           ?(($mode == 'compare')
           ?"<input type='image' src='/img/uncompare.gif' align='left'  title='Убрать из сравнения'
                    onClick='javascript: this.form.compare.value = \"del\"; this.form.submit();'>"
           :"<input type='image'  title='".(isset($_SESSION['compare'][$prod_id])?"Товар находиться в сравнении":"Добавить в сравнение")."' src='".(isset($_SESSION['compare'][$prod_id])?"/img/incompare.gif":"/img/compare.gif")."' align='left'
                    onClick='javascript: this.form.compare.value = 1; this.form.submit();'>")
           :"<input type='image' src='/img/uncart.gif' title='Удалить из корзины'
                    onClick='javascript: this.form.cart_prod_quant.value = 0; this.form.buy.value = 1; this.form.submit();'>").'
           </form>

          ';
  }

/*  Запись данных о купленных продуктах в сессию
*/
  function create_cart_session($data)
  {
      //session_name("user_cart");

      if (!is_array($_SESSION['user_cart']))
          $_SESSION['user_cart'] = array();

      if (!empty($data['cart_prod_quant']) &&
          !empty($data['cart_prod_id']))
      {
          if ($data['cart_prod_quant'] > $data['cart_prod_total'])
          {
              print '<script text="Javascript"> alert("В наличии только '.$data['cart_prod_total'].' шт."); document.location.href="/index.php?idCat='.$data['cart_cat_id'].'" </script>';
          }
          else
          {
              if (empty($_SESSION['user_cart'][$data['cart_prod_id']]))

                  $_SESSION['user_cart'] = $_SESSION['user_cart']+array($data['cart_prod_id'] => $data);

              if (!empty($_SESSION['user_cart'][$data['cart_prod_id']]))

                  $_SESSION['user_cart'][$data['cart_prod_id']]['cart_prod_quant'] = $data['cart_prod_quant'];
          }
      }
      elseif ( empty($data['cart_prod_quant']) &&
              !empty($data['cart_prod_id']) &&
              !empty($_SESSION['user_cart'][$data['cart_prod_id']]['cart_prod_quant']))

              unset($_SESSION['user_cart'][$data['cart_prod_id']]);
  }

  function create_compare_session($data)
  {
      if (!is_array($_SESSION['compare'])) $_SESSION['compare'] = array();

      if (is_array($data))
      {
          if (!$_SESSION['compare'][$data['cart_prod_id']])
              $_SESSION['compare'] = $_SESSION['compare']+array($data['cart_prod_id'] => $data);

          if ($data['compare'] == 'del' && $_SESSION['compare'][$data['cart_prod_id']])
              unset($_SESSION['compare'][$data['cart_prod_id']]);
      }
  }

  function compare_status($data)
  {
      if (is_array($data)) $total = count($data);

      if ($total > 0)
          return 'Товаров для сравнения: <a href="/compare.php"><b>'.$total.'</b></a>';
      else
          return 'Нет товаров в сравнении';
  }

/*  состояние корзины
*/
    function cart_status($array)
    {
       $total_price = 0;

       if (is_array($array)) foreach ($array as $k => $v)

           $total_price += $v['cart_prod_quant']*$v['cart_prod_price'];

       if (count($array) > 0)
           return "Товаров: <a href='/cart.php'><b>".count($array)."</b></a><br />
                   на сумму: <b>".$total_price." руб.</b><br />
                  ";
       else
           return "Ваша корзина пуста";
    }

/*  Проверка на пустой массив
*/
    function is_arr($array)
    {
        if (is_array($array))
        {
            $c = implode('',$array);

            if (!empty($c)) return true;
            else
                return false;
        }
    return false;
    }

/*  Корзина пользователя
*/
   function get_user_cart($array)
   {
       $html .=
       '<script language="javascript">

            $(".filter_conteiner").slideToggle();
            $(".filter_header").find("span").toggleClass("activebg");
        </script>';

       if (count($array) > 0)
       {
           $action = $_SERVER['REQUEST_URI'];
           $html .= '<script type="text/javascript">
                    window.kryotherm || (window.kryotherm = {});
                    window.kryotherm.cart ='. json_encode($array) .'
                </script>';
           $html .= '<div style="height: 54px"><a href="/">В каталог</a> > <a href="/cart.php" class="active">Корзина</a></div>
                     <h1>Корзина</h1>
                     <div class="clearfloat" style="width: 100%"></div>
                     <table width="100%" cellpadding="0" cellspacing="1" class="items user_cart">
                     <tr>
                         <th>№</th>
                         <th align="left">Название</th>
                         <th width="20">PDF</th>
                         <th align="left">Цена за 1шт.</th>
                         <th align="left">Количество</th>
                         <th align="left">Цена</th>
                     </tr>
                    ';

           $c = $total_cost = 0;
           foreach ($array as $k => $v)
           {
               $html .= '<tr '.((($c++)%2 == 0)?"":"class='grey'").'" style="clear: both; width: 100%">
                           <td align="center">'.($c).'.</td>
                           <td>'.(!empty($v['cart_prod_img'])?
                               '<a href="/index.php?idCat='.$v['cart_cat_id'].'">
                                <img src="'.A_PRODUCT_IMG_URL.'small_'.$v['cart_prod_img'].'" align="left" border="0" /></a>':'').'
                               <a href="/index.php?idCat='.$v['cart_cat_id'].'">'.$v['cart_prod_name'].'</a></td>

                           <td>'.get_pdf($v['cart_prod_pdf_file']).'&nbsp;</td>

                           <td>'.$v['cart_prod_price'].' <sub>руб.</sub></td>
                           <td>'.buy_form($action, $v['cart_prod_id'], $v['cart_prod_price'], $v['cart_prod_name'],
                                           $v['cart_prod_id'], $v['cart_prod_img'],
                                           $v['cart_prod_I_max'], $v['cart_prod_Q_max'], $v['cart_prod_U'], $v['cart_prod_R'], $v['cart_prod_A'],
                                           $v['cart_prod_B'], $v['cart_prod_C'], $v['cart_prod_D'], $v['cart_prod_H'],
                                           $v['cart_prod_Drus'], $v['cart_prod_di'],
                                           $v['cart_prod_sealing_type'], $v['cart_prod_HT'], $v['cart_prod_pdf_file'], $v['cart_prod_total'], '',$v['cart_prod_quant']).'

                           </td>
                           <td>'.($v['cart_prod_quant']*$v['cart_prod_price']).' <sub>руб.</sub></td>
                        </tr>';
               $total_cost += $v['cart_prod_quant']*$v['cart_prod_price'];
           }

           $html .= '</table>
                     <p><b>Итого: '.$total_cost.' руб.</b></p>
                     <div class="lessthen">Сумма заказа не может быть менее 1000руб.!</div>
                     <p><input type="button" value="Сделать заказ" '.(($total_cost < '1000')?'disabled':'').'
                               onClick="javascript: document.location.href=\'cart.php&exec_order=form\'">
                        <input type="button" value="Очистить"
                               onClick="javascript: document.location.href=\'cart.php&clear_cart=1\'">
                     </p>
                    ';

       }else
           $html .= 'Ваша корзина пуста';

   return $html;
   }

    function get_pdf($pdf)
    {
    return
    (!empty($pdf)
     ?'<a href="http://'.$_SERVER['SERVER_NAME'].'/pdf_docs/'.$pdf.'" target="_blanck" title="Скачать спецификацию">
          <img src="/img/small_pdf.gif" style="margin: 0px"></a>'
     :'');
    }

/*  Заказ покупателя
*/
    function customers_order($array)
    {
        if (is_arr($array))
        {
            $c = 0;
            $html .= '<table width="100%" cellpadding="0" cellspacing="1" class="items order">
                      <tr>
                          <th>№</th>
                          <th align="left">Название</th>
                          <th align="left">Количество</th>
                          <th align="left">Цена</th>
                      </tr>
                     ';

            foreach ($array as $k => $v)

               $html .= '<tr '.((($c++)%2 == 0)?"":"class='grey'").'>
                           <td align="center">'.($c).'.</td>
                           <td>'.$v['cart_prod_name'].'</td>
                           <td>'.$v['cart_prod_quant'].'x'.$v['cart_prod_price'].'</td>
                           <td>'.($v['cart_prod_quant']*$v['cart_prod_price']).' <sub>руб.</sub></td>
                        </tr>';

            $html .= '</table>';
        }
    return $html;
    }

//  вывод товара, в письме
    function letter_data($array)
    {
        $header = products_header_data();

        $html .= '<table border="1"><tr>';
        if (is_array($header)) foreach ($header as $k => $v)
        {
            $html .= '<th>'.$v.'</th>';
        }
        $html .= '<th>Всего</th></tr>';

        if (is_array($array)) foreach ($array as $k => $v)

            $html .=
            '<tr>
               <td>'.$v['cart_prod_name'].'</td>
               <td>'.$v['cart_prod_I_max'].'</td>
               <td>'.$v['cart_prod_Q_max'].'</td>
               <td>'.$v['cart_prod_U'].'</td>
               <td>'.$v['cart_prod_R'].'</td>
               <td>'.$v['cart_prod_A'].'</td>
               <td>'.$v['cart_prod_B'].'</td>
               <td>'.$v['cart_prod_C'].'</td>
               <td>'.$v['cart_prod_D'].'</td>
               <td>'.$v['cart_prod_H'].'</td>
               <td>'.$v['cart_prod_Drus'].'</td>
               <td>'.$v['cart_prod_di'].'</td>
               <td>&nbsp;'.$v['cart_prod_sealing_type'].'</td>
               <td>'.$v['cart_prod_HT'].'</td>
               <td>'.$v['cart_prod_quant'].'</td>
               <td>'.$v['cart_prod_price'].'</td>
               <td>'.($v['cart_prod_quant']*$v['cart_prod_price']).'</td>
             </tr>';

           $html .= '</table>';

    return $html;
    }


/*  Форма подтверждения заказа
*/
   function get_order_form($array, $customer)
   {
       if (is_arr($array))
       {
           $html .= "<div style='height: 54px'>
                     <a href='/'>В каталог</a> > <a href='/cart.php'>Корзина</a>
                      > <a href='/cart.php&exec_order=form' class='active'>Подтверждение заказа</a>
                     </div>
                     <h1>Ваш заказ</h1>".customers_order($array);
           $html .= '<script type="text/javascript">
                    window.kryotherm || (window.kryotherm = {});
                    window.kryotherm.cart ='. json_encode($array) .'
                    window.kryotherm.customer ='. json_encode($customer) .'
                </script>';

           $html .= '<br /><h2>Ваши данные:</h2>
                     <form name="order_form" action="/cart.php&exec_order=send" method="post">
                     <table width="60%" class="order_form">
                       <tr>
                           <td width="30%">Заказчик:</td>
                           <td><select size="1" name="customer" onChange="define_customer(this.value)">
                                 <option value="1" '.(($customer == 1)?'selected':'').'>Физическое лицо</option>
                                 <option value="2" '.(($customer == 2)?'selected':'').'>Юридическое лицо</option>
                               </select>
                           </td>
                       </tr>
                       <tr>
                           <td>Способ доставки:</td>
                           <td><select size="1" name="shipping">
                               <option value=""> --- </option>
                               <option value="СПСР-Экспресс">СПСР-Экспресс</option>
                               <option value="Грузовозов">Грузовозов</option>
                               <option value="Автотрейдинг">Автотрейдинг</option>
                               <option value="Почта России">Почта России</option>
                               <option value="Самовывоз">Самовывоз</option>
                               <option value="Другое">Другое</option>
                               </select>
                           </td>
                       </tr>
                     '.(($customer == '1')?'
                       <tr>
                           <td>Имя: <span class="active">*</span></td><td><input name="name" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>Отчество:</td><td><input name="patronymic" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>Фамилия: <span class="active">*</span></td><td><input name="surname" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>ИНН: <span class="active">*</span></td><td><input name="inn" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>Адрес грузополучателя: <span class="active">*</span></td><td><input name="adres" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>Телефон: <span class="active">*</span></td><td><input name="phone" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>Факс:</td><td><input name="fax" type="text" value=""></td>
                       </tr>
                       <tr>
                           <td>E-mail: <span class="active">*</span></td><td><input name="mail" type="text" value=""></td>
                       </tr>
                       ':'
                       <tr>
                           <td>Полное название организации: <span class="active">*</span></td>
                           <td><input type="text" value="" name="organisation"></td>
                       </tr>
                       <tr>
                           <td>ИНН: <span class="active">*</span></td>
                           <td><input type="text" value="" name="inn"></td>
                       </tr>
                       <tr>
                           <td>КПП: <span class="active">*</span></td>
                           <td><input type="text" value="" name="kpp"></td>
                       </tr>
                       <tr>
                           <td>ОКПО:</td>
                           <td><input type="text" value="" name="okpo"></td>
                       </tr>
                       <tr>
                           <td>Юридический адрес: <span class="active">*</span></td>
                           <td><input type="text" value="" name="jaddress"></td>
                       </tr>
                       <tr>
                           <td>Фактический адрес: <span class="active">*</span></td>
                           <td><input type="text" value="" name="postaladdress"></td>
                       </tr>
                       <tr>
                           <td>Банковские реквизиты: <span class="active">*</span></td>
                           <td><input type="text" value="" name="bank"></td>
                       </tr>
                       <tr>
                           <td>ФИО Ген. директора: <span class="active">*</span></td>
                           <td><input type="text" value="" name="gendir"></td>
                       </tr>
                       <tr>
                           <td>Контактное лицо:</td>
                           <td><input type="text" value="" name="contactperson"></td>
                       </tr>
                       <tr>
                           <td>Телефон: <span class="active">*</span></td>
                           <td><input type="text" value="" name="phone"></td>
                       </tr>
                       <tr>
                           <td>Fax:</td>
                           <td><input type="text" value="" name="fax"></td>
                       </tr>
                       <tr>
                           <td>Email: <span class="active">*</span></td>
                           <td><input type="text" value="" name="mail"></td>
                       </tr>
                       ').'
                       <tr>
                           <td>Комментарии пользователя:</td>
                           <td><textarea name="descript"></textarea></td>
                       </tr>
                       <tr>
                          <td colspan="2" class="vam">
                            <span class="active">*</span> &mdash; поля обязательные для заполнения.
                          </td>
                       </tr>
                       <tr>
                           <td></td>
                           <td><input type="button" value="Подтвердить заказ" onClick="validate();"></td>
                       </tr>
                     </table>
                     </form><br />

                     ';

       }else
           $html .= "Пустой заказ";

   return $html;
   }

   function exec_order_form($mail, $send_data, &$order_data)
   {
       $html .="<div style='height: 54px'>
                <a href='/'>В каталог</a> > <a href='/cart.php'>Корзина</a> >
                <a href='/cart.php&exec_order=form'>Подтверждение закза</a> > ";

       if (!empty($send_data['email'])) $send_data['mail'] = $send_data['email'];

       if (is_arr($send_data) && !empty($send_data['mail']))
       {
           if ($send_data['customer'] == 1)
           {
               $customer = 'Физическое лицо';
               $mess = "<p>Здравствуйте
                       ".$send_data['name']."
                       ".(!empty($send_data['patronymic'])?$send_data['patronymic']:"")."
                       ".$send_data['surname'].".</p>
                        <p>Вы сделали заказ на сайте ".$_SERVER['HTTP_HOST']."</p>";
           }
           else
           {
               $customer = 'Юридическое лицо';
               $mess = 'Здравствуйте
                       '.(!empty($send_data['contactperson'])?$send_data['contactperson']:$send_data['organisation']).'
                        <p>Вы сделали заказ на сайте '.$_SERVER['HTTP_HOST'].'</p>';
           }

           $mess = $mess."<p>Способ доставки: ".$send_data['shipping']."</p><p>Ваш заказ:</p>
                   ".letter_data($order_data);
             /*
                   $from="FROM: ".SHOP_EMAIL." \nContent-Type: text/html; charset=windows-1251\nContent-Transfer-Encoding: 8bit";

                   print_r($send_data);
                   if (!mail($send_data['mail'],"Заказ с сайта", $mess, $from)) die ('ошибка');;
       */
                   $mail->From = SHOP_EMAIL;
           $mail->FromName = 'Zakaz s saita '.$_SERVER['HTTP_HOST'];
           $mail->AddAddress($send_data['mail']);
           $mail->IsHTML(true);
           $mail->Subject = 'Zakaz s saita '.$_SERVER['HTTP_HOST'];
           $mail->Body = $mess;

           if (!$mail->Send()) die ('Mailer Error1: '.$mail->ErrorInfo);

           $data_definer = order_data_definer();

           $customer_data = null;

           if (is_array($send_data)) foreach ($send_data as $k => $v)

               if (!empty($v) && !empty($data_definer[$k]))
                   $customer_data .= $data_definer[$k].': '.$v.'<br />';


           $mess = "<p>С сайта ".$_SERVER['HTTP_HOST']." поступил заказ:</p>
                   ".letter_data($order_data).
                   "<p>Заказчик: ".$customer."</p>
                    <p>".$customer_data."</p>";

           $mail->From = $send_data['mail'];
           $mail->FromName = 'Заказ с сайта '.$_SERVER['HTTP_HOST'];
           $mail->AddAddress(SHOP_EMAIL);
           $mail->IsHTML(true);
           $mail->Subject = 'Заказ с сайта '.$_SERVER['HTTP_HOST'];
           $mail->Body = $mess;

           if (!$mail->Send()) die ('Mailer Error2: '.$mail->ErrorInfo);

           //$order_data = array();

           $html .= '<span class="active">Успешно отправлено</span></div>
                     <p>Ваш заказ успешно отправлен на обработку.</p>
                    ';
       }
       else
           $html .= '<span class="active">Заполните обязательные поля</span></div>
                     <p>Ошибка при заполнении полей формы подтверждения заказа</p>
                    ';

   return $html;
   }

   function order_data_definer()
   {
   return array('shipping' => 'Способ доставки',
                'organisation' => 'Полное название организации:',
                'inn' => 'ИНН',
                'kpp' => 'КПП',
                'okpo' => 'ОКПО',
                'jaddress' => 'Юридический адрес',
                'postaladdress' => 'Фактический адрес',
                'bank' => 'Банковские реквизиты',
                'gendir' => 'ФИО Ген. директора',
                'contactperson' => 'Контактное лицо',
                'phone' => 'Телефон',
                'fax' => 'Факс',
                'email' => 'Email',
                'descript' => 'Комментарии пользователя',
                'patronymic' => 'Отчество',
                'surname' => 'Фамилия',
                'adres' => 'Адрес грузополучателя',
                'name' => 'Имя',
                'mail' => 'E-mail'
               );
   }

    function sort_array($array, $order, $mode = '')
    {


        $sort_type = strrchr($order, '_');

        if ($sort_type == '_asc') $sort = SORT_ASC;
        elseif ($sort_type == '_desc') $sort = SORT_DESC;

        $field = str_replace($sort_type, '', $order);

        if (is_array($array)) foreach ($array as $k => $v)
        {
            if ($mode = 'compare') $v = compare_values($v);

            $result[$k] = array_merge(array($field => $v[$field]), $v);
        }

        if (is_array($result))
            array_multisort($result, $sort);

    return $result;
    }

    function compare_values($v)
    {
        array_walk($v, 'prepare_values');
        $v = array_flip($v);

        array_walk($v, 'prepare_keys');
        $v = array_flip($v);

        array_walk($v, 'process_values');

    return $v;
    }

    function prepare_keys(&$item, $key)
    {
        $item = str_replace(array('cart_prod_','total'),array('','quant'),$item);
    }

    function prepare_values(&$item, $key)
    {
        $item = (string)$item.'+'.uniqid();
    }

    function process_values(&$item, $key)
    {
        $item = preg_replace('/^([A-Za-zА-Яа-я\d\s\-,\.\/\(\)]*)(\+\d+\w+)/i','$1',$item);
    }


?>