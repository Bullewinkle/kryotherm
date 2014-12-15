<?
  class Catalog
  {
        var $destination=null,$url=null,$s_w=null,$s_h=null,$m_w=null,$m_h=null,$maxImgSize=null,$idPar=null,$site_db_link=null;

        function __construct($auth,$common){

                $this->auth = $auth;
                $this->common = $common;
        }

        function caseDefiner($case){

                   switch($case){

                           case "category": $this->destination = A_CATALOG_IMG_DIR;
                                            $this->url = A_CATALOG_IMG_URL;
                                            $this->s_w = A_CATALOG_SMALL_IMG_W;
                                            $this->s_h = A_CATALOG_SMALL_IMG_H;
                                            $this->m_w = A_CATALOG_MID_IMG_W;
                                            $this->m_h = A_CATALOG_MID_IMG_H;
                                            $this->maxImgSize = A_CATALOG_IMG_SIZE;
                                            $this->table = 'catalog_categories';
                                            $this->pdf_pass = A_CATALOG_PDF_PASS;
                                 break;

                           case "product":  $this->destination = A_PRODUCT_IMG_DIR;
                                            $this->url = A_PRODUCT_IMG_URL;
                                            $this->s_w = A_PRODUCT_SMALL_IMG_W;
                                            $this->s_h = A_PRODUCT_SMALL_IMG_H;
                                            $this->m_w = A_PRODUCT_MID_IMG_W;
                                            $this->m_h = A_PRODUCT_MID_IMG_H;
                                            $this->maxImgSize = A_PRODUCT_IMG_SIZE;
                                            $this->table = 'catalog_products';
                                 break;

                           case "manufacturers":
                                            $this->destination = A_MANUFACTURERS_IMG_DIR;
                                            $this->url = A_MANUFACTURERS_IMG_URL;
                                            $this->s_w = A_MANUFACTURERS_SMALL_IMG_W;
                                            $this->s_h = A_MANUFACTURERS_SMALL_IMG_H;
                                            $this->m_w = A_MANUFACTURERS_MID_IMG_W;
                                            $this->m_h = A_MANUFACTURERS_MID_IMG_H;
                                            $this->maxImgSize = A_MANUFACTURERS_IMG_SIZE;
                                            $this->table = 'catalog_manufacturers';
                                 break;
                   }
        }

        function getCatalog($idCat,$idPar,$idPrev){

                list($id, $id_parent, $name, $created, $modified, $published, $sort_order, $pdf_file, $par, $prod)=
                $this->auth->QueryExecute("SELECT c1.*,c2.id, p2c.id_product
                                           FROM ".TBL_PREF."catalog_categories c1
                                           LEFT JOIN ".TBL_PREF."catalog_categories c2 ON c1.id=c2.id_parent
                                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON c1.id=p2c.id_category
                                           WHERE c1.id_parent='".$idCat."' GROUP BY c1.id ORDER BY c1.title, c1.sort_order ", array(0,1,2,4,5,6,7,13,14,15));

                $html .= '<table width="100%" cellpadding="2" cellspacing="0">
                          <tr><th width="2">&nbsp;</th><th class="tal">Название категории / товара</th><th>PDF</th><th>Отображение</th><th>Действия</th></tr>';

                $row = 0;
                if(is_array($id)) foreach($id as $k=>$v)

                   $html .= '<tr '.((($row++)%2 == 0)?'class="dataTableRow1"':'').' >
                              <td><div><a title="Вверх" href="index.php?action=moveUp_category&move='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'&sort_order='.$sort_order[$k].'"><img width="8" height="6" border="0" alt="Вверх" src="images/icons/up.gif"></a></div>
                                  <div style="padding-top: 3px;"><a title="Вниз" href="index.php?action=moveDown_category&move='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'&sort_order='.$sort_order[$k].'"><img width="8" height="6" border="0" alt="Вниз" src="images/icons/down.gif"></a></div>
                              </td>
                              <td><a href="?action=catalog&idCat='.$v.'&idPar='.$id_parent[$k].'&idPrev='.$idPar.'">'.$name[$k].'</a></td>
                              <td align="center">'.$pdf_file[$k].'</td>
                              <td class="tac">'.(!empty($published[$k])?'
                                             <img width="18" height="18" border="0" src="images/icon_status_green.gif" alt="Активный" title=" Активный ">&nbsp;&nbsp;&nbsp;
                                             <a href="index.php?action=lock_category&lock='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="18" height="18" border="0" src="images/icon_status_red_light.gif" alt="Сделать неактивным" title=" Сделать неактивным "></a>':'
                                             <a href="index.php?action=unlock_category&lock='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="18" height="18" border="0" src="images/icon_status_green_light.gif" alt="Активизировать" title=" Активизировать "></a>&nbsp;&nbsp;&nbsp;
                                             <img width="18" height="18" border="0" src="images/icon_status_red.gif" alt="Неактивный" title=" Неактивный ">').'</td>
                             <td class="tac">
                                 '.((($_REQUEST['case'] == 'categories') and ($_REQUEST['replace'] == $v))?$this->getCategoryList('javascript:if(confirm(\'Уверены что хотите этого?\'))this.form.submit();','',$v,'replace_category',$v)
                                 :'<a href="?action=edit_category&idCat='.$v.'&idPar='.$id_parent[$k].'&idPrev='.$idPar.'"><img width="22" height="22" border="0" alt="Редактировать" src="images/icons/ico_edit.gif"></a>
                                 <a title="Переместить" href="?action=catalog&case=categories&replace='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="43" height="22" border="0" alt="Переместить" src="images/icons/move.gif"></a>
                                 '.((!empty($par[$k]) or !empty($prod[$k]))?'<img width="22" height="22" border="0" title="Нельзя удалить - удалите связанные страницы" alt="Нельзя удалить - удалите связанные категории или товары" src="images/icons/b_cancel.gif">':'<a href="javascript: void[0];" onClick="javascript: if(confirm(\'Вы действительно хотите удалить категорию '.$name[$k].' ? \'))document.location.href=\'index.php?action=delete_category&delete='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'\';" ><img width="22" height="22" border="0" alt="Удалить" src="images/icons/ico_del.gif"></a>')
                                 ).'
                             </td>
                             </tr>';

                list($pId, $pName, $pCreated, $pModified, $pPublished, $pSort_order)=
                $this->auth->QueryExecute("SELECT p.* FROM ".TBL_PREF."catalog_products p
                                           LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                                           WHERE p2c.id_category='".$idCat."' GROUP BY p.id ORDER BY p.sort_order ", array(0,3,5,6,7,8));

                if(is_array($pId)) foreach($pId as $k=>$v)

                   $html .= '<tr '.((($row++)%2 == 0)?'class="dataTableRow1"':'').' >
                              <td><div><a title="Вверх" href="index.php?action=moveUp_product&move='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'&sort_order='.$pSort_order[$k].'"><img width="8" height="6" border="0" alt="Вверх" src="images/icons/up.gif"></a></div>
                                  <div style="padding-top: 3px;"><a title="Вниз" href="index.php?action=moveDown_product&move='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'&sort_order='.$pSort_order[$k].'"><img width="8" height="6" border="0" alt="Вниз" src="images/icons/down.gif"></a></div>
                              </td>
                              <td>'.$pName[$k].'</td>
                              <td class="tac">'.(!empty($pPublished[$k])?'
                                             <img width="18" height="18" border="0" src="images/icon_status_green.gif" alt="Активный" title=" Активный ">&nbsp;&nbsp;&nbsp;
                                             <a href="index.php?action=lock_product&lock='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="18" height="18" border="0" src="images/icon_status_red_light.gif" alt="Сделать неактивным" title=" Сделать неактивным "></a>':'
                                             <a href="index.php?action=unlock_product&lock='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="18" height="18" border="0" src="images/icon_status_green_light.gif" alt="Активизировать" title=" Активизировать "></a>&nbsp;&nbsp;&nbsp;
                                             <img width="18" height="18" border="0" src="images/icon_status_red.gif" alt="Неактивный" title=" Неактивный ">').'</td>
                             <td class="tac">
                             '.((($_REQUEST['case'] == 'product') and ($_REQUEST['replace'] == $v))?$this->getCategoryList('javascript:if(confirm(\'Уверены что хотите этого?\'))this.form.submit();','','','replace_product',$v)
                               :'<a href="?action=edit_product&idProd='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="22" height="22" border="0" alt="Редактировать" src="images/icons/ico_edit.gif"></a>
                                 <a title="Переместить" href="?action=catalog&case=product&replace='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'"><img width="43" height="22" border="0" alt="Переместить" src="images/icons/move.gif"></a>
                                 <a href="javascript: void[0];" onClick="javascript: if(confirm(\'Вы действительно хотите удалить '.$pName[$k].' ? \'))document.location.href=\'index.php?action=delete_product&delete='.$v.'&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev.'\';" ><img width="22" height="22" border="0" alt="Удалить" src="images/icons/ico_del.gif"></a>').'
                             </td>
                             </tr>';

                if(!is_array($id) and !is_array($pId))
                    $html .= '<tr class="dataTableRow1"><td colspan="4" class="tac"> Нет товаров и категорий </td></tr>';

                $html .= '</table>';

        return $html;
        }

        function getManufacturers(){

                list($id, $name, $created, $modified, $published, $sort_order, $summ)=
                $this->auth->QueryExecute("SELECT m.*, COUNT(DISTINCT p.id) as summ FROM ".TBL_PREF."catalog_manufacturers m
                                           LEFT JOIN ".TBL_PREF."catalog_products p ON m.id=p.id_manufacturers
                                           GROUP BY m.id ORDER BY m.sort_order ", array(0,1,3,4,5,6,12));

                $html .= '<table width="100%" cellpadding="2" cellspacing="0">
                          <tr><th width="2">&nbsp;</th><th class="tal">Название производителя</th><th>Количество товаров</th><th width="100">Отображение</th><th width="100">Действия</th></tr>';

                $row = 0;
                if(is_array($id)) foreach($id as $k=>$v)

                   $html .= '<tr '.((($row++)%2 == 0)?'class="dataTableRow1"':'').' >
                              <td><div><a title="Вверх" href="index.php?action=moveUp_manufacturers&move='.$v.'&sort_order='.$sort_order[$k].'"><img width="8" height="6" border="0" alt="Вверх" src="images/icons/up.gif"></a></div>
                                  <div style="padding-top: 3px;"><a title="Вниз" href="index.php?action=moveDown_manufacturers&move='.$v.'&sort_order='.$sort_order[$k].'"><img width="8" height="6" border="0" alt="Вниз" src="images/icons/down.gif"></a></div>
                              </td>
                              <td>'.$name[$k].'</td>
                              <td class="tac">'.(!empty($summ[$k])?'<a href="?action=manufacturers&idManuf='.$v.'">'.$summ[$k].' ед.</a>':'нет товаров').'</td>
                              <td class="tac">'.(!empty($published[$k])?'
                                             <img width="18" height="18" border="0" src="images/icon_status_green.gif" alt="Активный" title=" Активный ">&nbsp;&nbsp;&nbsp;
                                             <a href="index.php?action=lock_manufacturers&lock='.$v.'"><img width="18" height="18" border="0" src="images/icon_status_red_light.gif" alt="Сделать неактивным" title=" Сделать неактивным "></a>':'
                                             <a href="index.php?action=unlock_manufacturers&lock='.$v.'"><img width="18" height="18" border="0" src="images/icon_status_green_light.gif" alt="Активизировать" title=" Активизировать "></a>&nbsp;&nbsp;&nbsp;
                                             <img width="18" height="18" border="0" src="images/icon_status_red.gif" alt="Неактивный" title=" Неактивный ">').'</td>
                             <td class="tac"><a href="?action=edit_manufacturers&idManuf='.$v.'"><img width="22" height="22" border="0" alt="Редактировать" src="images/icons/ico_edit.gif"></a>
                                 '.(!empty($summ[$k])?'<img width="22" height="22" border="0" title="Нельзя удалить - есть связанные с производителем товары" alt="Нельзя удалить - есть связанные с производителем товары" src="images/icons/b_cancel.gif">':'<a href="javascript: void[0];" onClick="javascript: if(confirm(\'Вы действительно хотите удалить производителя '.$name[$k].' ? \'))document.location.href=\'index.php?action=delete_manufacturers&delete='.$v.'\';" ><img width="22" height="22" border="0" alt="Удалить" src="images/icons/ico_del.gif"></a>').'
                             </td>
                             </tr>';

                    if(!is_array($id))
                     $html .= '<tr class="dataTableRow1"><td colspan="5" class="tac"> Нет производителей </td></tr>';

                     $html .= '</table>';
        return $html;
        }

        function getProductsList($idManuf){

                list($pId, $pName, $pCreated, $pModified, $pPublished, $pSort_order, $pManName)=
                $this->auth->QueryExecute("SELECT * FROM ".TBL_PREF."catalog_products WHERE id_manufacturers='".$idManuf."' ".(!empty($_REQUEST['find'])?"AND name LIKE '%".$_REQUEST['find']."%'":"")." ORDER BY name ", array(0,3,5,6,7,8,14));

                $html .= '<table width="100%" cellpadding="2" cellspacing="0">
                          <tr><th width="2">№</th><th class="tal">Название товара</th><th width="100">Отображение</th><th width="100">Действия</th></tr>';

                if(is_array($pId)) foreach($pId as $k=>$v)

                   $html .= '<tr '.((($row++)%2 == 0)?'class="dataTableRow1"':'').' >
                              <td><b>'.($k+1).'.</b></td>
                              <td>'.(!empty($_REQUEST['find'])?str_replace($_REQUEST['find'],'<span class="green">'.$_REQUEST['find'].'</span>',$pName[$k]):$pName[$k]).'</td>
                              <td class="tac">'.(!empty($pPublished[$k])?'
                                             <img width="18" height="18" border="0" src="images/icon_status_green.gif" alt="Активный" title=" Активный ">&nbsp;&nbsp;&nbsp;
                                             <a href="index.php?action=lock_product&lock='.$v.'&idManuf='.$idManuf.'"><img width="18" height="18" border="0" src="images/icon_status_red_light.gif" alt="Сделать неактивным" title=" Сделать неактивным "></a>':'
                                             <a href="index.php?action=unlock_product&lock='.$v.'&idManuf='.$idManuf.'"><img width="18" height="18" border="0" src="images/icon_status_green_light.gif" alt="Активизировать" title=" Активизировать "></a>&nbsp;&nbsp;&nbsp;
                                             <img width="18" height="18" border="0" src="images/icon_status_red.gif" alt="Неактивный" title=" Неактивный ">').'</td>
                             <td class="tac"><a href="?action=edit_product&idProd='.$v.'&idManuf='.$idManuf.'"><img width="22" height="22" border="0" alt="Редактировать" src="images/icons/ico_edit.gif"></a>

                                 <a href="javascript: void[0];" onClick="javascript: if(confirm(\'Вы действительно хотите удалить '.$pName[$k].' ? \'))document.location.href=\'index.php?action=delete_product&delete='.$v.'&idManuf='.$idManuf.'\';" ><img width="22" height="22" border="0" alt="Удалить" src="images/icons/ico_del.gif"></a>
                             </td>
                             </tr>';

                if(!is_array($id) and !is_array($pId))
                    $html .= '<tr class="dataTableRow1"><td colspan="4" class="tac"> Нет товаров и категорий </td></tr>';

                $html .= '</table>';

        return $html;

        }

    function connect_to_site_db()
    {
        $host = 'localhost';
        $user = 'kryother';
        $psw  = 'b333';
        $db   = 'kryother_ru';

        $this->site_db_link = mysql_connect($host,$user,$psw);
        mysql_select_db($db, $this->site_db_link);
    }

    function disconnect_from_site_db()
    {
        mysql_close($this->site_db_link);
    }

    function get_shop_page_list($def = '')
    {
        $this->connect_to_site_db();
        $sql = "select id, name from sitemenu order by name";
        $q   = mysql_query($sql, $this->site_db_link);

        $site_pages_list = '';
        $site_pages_list = '<select size="1" name="tid"><option value=""> --- </option>';

        while ( $r = mysql_fetch_assoc($q) )
        {
            $site_pages_list .= '<option value="'.$r['id'].'" '.($r['id'] == $def?'selected':'').'>'.$r['name'].'</option>';
        }

        $site_pages_list .= '</select>';

    return $site_pages_list;
    }


    function CatalogCategoryForm($mode,$idCat='',$idPar=''){

                if($mode == 'edit')
                   list($id,$id_parent,$name,$text,$image,$title,$descr,$keyw,$pdf_file, $tid)=
                   $this->auth->QueryExecute("SELECT * FROM ".TBL_PREF."catalog_categories WHERE id='".$idCat."'", array(0,1,2,3,8,9,10,11,13,15));

                $html .= '<table width="100%" id="editor">
                           <tr><td width="117">Название</td>
                               <td><input style="width: 100%" name="name" type="text" value="'.$name[0].'"></td>
                               <td rowspan="4" class="tac" style="width:30%">
                               '.(empty($image[0])?'
                               <input type="hidden" value="'.$this->maxImgSize.'" name="MAX_FILE_SIZE">
                               <input name="userfile" type="file" value="">':
                               '<img src='.$this->url.'medium_'.$image[0].'>
                                <p><input name="delImg" type="checkbox" value="ON"> удалить изображение</p>
                               ').'
                               </td>
                           </tr>
                           <tr><td>Описание</td>
                               <td>
<script language="javascript">
<!--//
var currentTextArea = null
function openEditor(textarea) {

        // location of edit.php file:
        var editFile = \'/c0ntr0lz0ne/editor_files/edit.php\';

        currentTextArea = textarea;

        var edit = window.open(editFile, \'editorWindow\', \'width=720, height=450\');
        edit.focus();
}
//-->
</script>
          <a href="javascript:openEditor(document.category.text)" title="Визуальный редактор"><img src="images/icons/redaktor.gif" align=middle width="27" height="27" alt="Визуальный редактор"></a>
          <a href="javascript:openEditor(document.category.text)" title="Визуальный редактор" style="font-size:12px; vertical-align:middle;">Визуальный редактор</a>
                               <textarea style="width: 100%" rows="10" name="text">'.$text[0].'</textarea></td></tr>
                           <tr><td>PDF</td>
                               <td>
                               '.(empty($pdf_file[0])?'
                               <input name="pdf_file" type="file" value="">':
                                $pdf_file[0].'
                                <p><input name="del_pdf" type="checkbox" value="ON"> удалить документ</p>
                               ').'
                               </td></tr>
                           <!--
                           <tr><td>Привязка к странице:</td>
                               <td>'.$this->get_shop_page_list($tid[0]).'</td>
                           </tr>
                             //-->
                           <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
                           <tr><td>Title</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="title">'.$title[0].'</textarea></td></tr>
                           <tr><td>Description</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="description">'.$descr[0].'</textarea></td></tr>
                           <tr><td>Keywords</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="keywords">'.$keyw[0].'</textarea></td></tr>
                          </table>';
        return $html;
        }

        function CatalogProductForm($mode,$idProd=''){


                if($mode == 'edit')
                   list($id,$id_manuf,$price,$name,$text,$image,$title,$desc,$keyw)=
                   $this->auth->QueryExecute("SELECT * FROM ".TBL_PREF."catalog_products WHERE id='".$idProd."' ", array(0,1,2,3,4,9,10,11,12));

                $html .= '<table width="100%" id="editor">
                           <tr><td width="117">Название</td>
                               <td><input style="width: 100%" name="name" type="text" value="'.$name[0].'"></td>
                               <td rowspan="4" class="tac" style="width:30%">
                               '.(empty($image[0])?'
                               <input type="hidden" value="'.$this->maxImgSize.'" name="MAX_FILE_SIZE">
                               <input name="userfile" type="file" value="">':
                               '<img src='.$this->url.'medium_'.$image[0].'>
                                <p><input name="delImg" type="checkbox" value="ON"> удалить изображение</p>
                               ').'
                               </td>
                           </tr>
                           <tr><td>Цена</td>
                               <td><input name="price" type="text" value="'.$price[0].'"></td>
                           <tr><td>Производитель</td>
                               <td><select size="1" name="manufacturers"><option value="">не установлен</option>'.$this->getManufacturersList($id_manuf[0]).'</select></td></tr>
                           <tr><td>Описание</td>
                               <td><textarea style="width: 100%" rows="10" name="text">'.$text[0].'</textarea></td></tr>

                           <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
                           <tr><td>Title</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="title">'.$title[0].'</textarea></td></tr>
                           <tr><td>Description</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="description">'.$desc[0].'</textarea></td></tr>
                           <tr><td>Keywords</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="keywords">'.$keyw[0].'</textarea></td></tr>
                          </table>';
        return $html;
        }

        function CatalogManufacturersForm($mode,$idManuf=''){


                if($mode == 'edit')
                   list($id,$name,$text,$image,$title,$desc,$keyw)=
                   $this->auth->QueryExecute("SELECT * FROM ".TBL_PREF."catalog_manufacturers WHERE id='".$idManuf."' ", array(0,1,2,7,8,9,10));

                $html .= '<table width="100%" id="editor">
                           <tr><td width="117">Название</td>
                               <td><input style="width: 100%" name="name" type="text" value="'.$name[0].'"></td>
                               <td rowspan="2" class="tac" style="width:30%">
                               '.(empty($image[0])?'
                               <input type="hidden" value="'.$this->maxImgSize.'" name="MAX_FILE_SIZE">
                               <input name="userfile" type="file" value="">':
                               '<img src='.$this->url.'medium_'.$image[0].'>
                                <p><input name="delImg" type="checkbox" value="ON"> удалить изображение</p>
                               ').'
                               </td>
                           </tr>
                           <tr><td>Описание</td>
                               <td><textarea style="width: 100%" rows="10" name="text">'.$text[0].'</textarea></td></tr>

                           <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
                           <tr><td>Title</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="title">'.$title[0].'</textarea></td></tr>
                           <tr><td>Description</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="description">'.$desc[0].'</textarea></td></tr>
                           <tr><td>Keywords</td>
                               <td colspan="2"><textarea style="width: 100%" rows="2" name="keywords">'.$keyw[0].'</textarea></td></tr>
                          </table>';
        return $html;
        }

        function processingCategoryForm($mode,$idCat='',$idPar='',$idPrev=''){

                  $name = $this->common->prepareString($_REQUEST['name']);
                  $text = $this->common->prepareString($_REQUEST['text']);
                  $title = $this->common->prepareString($_REQUEST['title']);
                  $desc = $this->common->prepareString($_REQUEST['description']);
                  $keyw = $this->common->prepareString($_REQUEST['keywords']);
                  $tid  = $_REQUEST['tid'];
                  $user = 1;

                  if($mode == 'insert'){
                      if(!$this->categoryExists($name,$idCat) and !empty($name)){

                          $this->auth->db->Execute("INSERT INTO ".TBL_PREF."catalog_categories SET
                                                    id_parent='".$idCat."', name='".$name."',
                                                    story_text='".$text."', created='".date('Y-m-d')."',
                                                    modified='".date('Y-m-d')."',published='1',sort_order='".$this->defineSortOrder($idCat)."',
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."',tid='".$tid."'");

                          $catId = mysql_insert_id();

                          if ($pdf_file = $this->file_processing($catId))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET pdf_file='".$pdf_file."' WHERE id= ".$catId."");

                          if($image = $this->imageProcessing($catId))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET image='".$image."' WHERE id= ".$catId."");

                          $a_idCat=$catId; $a_idPar=$idCat; $a_idPrev=$idPar;

                      }
                  }
                  elseif($mode == 'update'){

                          $unlink_pdf = 0;

                          if (!empty($_REQUEST['del_pdf']))
                          {
                              @unlink($this->pdf_pass.$idCat.".pdf");
                              $unlink_pdf = 1;
                          }

                          $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET
                                                    name='".$name."',
                                                    story_text='".$text."',
                                                    modified='".date('Y-m-d')."', ".(!empty($_REQUEST['delImg'])?$this->imageProcessing($idCat,'del')."image='', ":"")."
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."',tid='".$tid."'
                                                   ".(!empty($unlink_pdf)?",pdf_file=''":"")."
                                                    WHERE id='".$idCat."'");

                          if ($pdf_file = $this->file_processing($idCat))
                              $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET pdf_file='".$pdf_file."' WHERE id= ".$idCat."");


                          if($image = $this->imageProcessing($idCat))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET image='".$image."' WHERE id= ".$idCat."");



                          $a_idCat=$idCat; $a_idPar=$idPar; $a_idPrev=$idPrev;
                          $idCat=$idPar; $idPar=$idPrev; $idPrev=$this->idPar;
                  }

                  if(!empty($_REQUEST['apply']))
                      $this->common->redirect("index.php?action=edit_category&idCat=".$a_idCat."&idPar=".$a_idPar."&idPrev=".$a_idPrev);
                  else
                      $this->common->redirect("index.php?action=catalog&idCat=".$idCat."&idPar=".$idPar."&idPrev=".$idPrev);
        }

        function file_processing($idCat)
        {
            if (!empty($_FILES['pdf_file']['tmp_name']))
            {
                switch ($_FILES['pdf_file']['type'])
                {
                    case "application/pdf":

                        $filename = $this->pdf_pass.$idCat.".pdf";
                        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $filename))

                            return $idCat.".pdf";
                        else
                            return false;
                    break;
                    default: return false;
                }
            }
            else
                return false;
        }

        function processingProductsForm($mode,$idProd='',$idCat='',$idPar='',$idPrev=''){

                  $name = $this->common->prepareString($_REQUEST['name']);
                  $price = $this->common->prepareString($_REQUEST['price']);
                  $id_man = $_REQUEST['manufacturers'];
                  $text = $this->common->prepareString($_REQUEST['text']);
                  $title = $this->common->prepareString($_REQUEST['title']);
                  $desc = $this->common->prepareString($_REQUEST['description']);
                  $keyw = $this->common->prepareString($_REQUEST['keywords']);
                  $user = 1;

                  if($mode == 'insert'){
                      if(!$this->productExists($name,$idCat) and !empty($name)){

                          $this->auth->db->Execute("INSERT INTO ".TBL_PREF."catalog_products SET
                                                    id_manufacturers='".$id_man."', price='".$price."', name='".$name."',
                                                    story_text='".$text."', created='".date('Y-m-d')."',
                                                    modified='".date('Y-m-d')."',published='1',sort_order='".$this->defineSortOrder($idCat)."',
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."'");

                          $idProd = mysql_insert_id();

                          $this->auth->db->Execute("INSERT INTO ".TBL_PREF."catalog_p2c SET id_category='".$idCat."',id_product='".$idProd."'");

                          if($image = $this->imageProcessing($idProd))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_products SET image='".$image."' WHERE id= ".$idProd."");
                      }
                  }
                  elseif($mode == 'update'){

                          $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_products SET
                                                    id_manufacturers='".$id_man."', price='".$price."', name='".$name."',
                                                    story_text='".$text."',
                                                    modified='".date('Y-m-d')."', ".(!empty($_REQUEST['delImg'])?$this->imageProcessing($idProd,'del')."image='', ":"")."
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."' WHERE id='".$idProd."'");

                          if($image = $this->imageProcessing($idProd))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_products SET image='".$image."' WHERE id= ".$idProd."");
                  }

                  if(empty($_REQUEST['idManuf'])){

                   if(!empty($_REQUEST['apply'])) $this->common->redirect("index.php?action=edit_product&idProd=".$idProd."&idCat=".$idCat."&idPar=".$idPar."&idPrev=".$idPrev);
                   else $this->common->redirect("index.php?action=catalog&idCat=".$idCat."&idPar=".$idPar."&idPrev=".$idPrev);

                  }else{

                   if(!empty($_REQUEST['apply'])) $this->common->redirect("index.php?action=edit_product&idProd=".$idProd."&idManuf=".$_REQUEST['idManuf']);
                   else $this->common->redirect("index.php?action=manufacturers&idManuf=".$_REQUEST['idManuf']);
                  }
        }

        function processingManufacturersForm($mode,$idManuf=''){

                  $name = $this->common->prepareString($_REQUEST['name']);
                  $text = $this->common->prepareString($_REQUEST['text']);
                  $title = $this->common->prepareString($_REQUEST['title']);
                  $desc = $this->common->prepareString($_REQUEST['description']);
                  $keyw = $this->common->prepareString($_REQUEST['keywords']);
                  $user = 1;

                  if($mode == 'insert'){
                      if(!$this->manufacturersExists($name) and !empty($name)){

                          $this->auth->db->Execute("INSERT INTO ".TBL_PREF."catalog_manufacturers SET
                                                    name='".$name."',
                                                    story_text='".$text."', created='".date('Y-m-d')."',
                                                    modified='".date('Y-m-d')."',published='1',sort_order='".$this->defineSortOrder()."',
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."'");

                          $idManuf = mysql_insert_id();

                          if($image = $this->imageProcessing($idManuf))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_manufacturers SET image='".$image."' WHERE id= ".$idManuf."");
                      }
                  }
                  elseif($mode == 'update'){

                          $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_manufacturers SET
                                                    name='".$name."', story_text='".$text."',
                                                    modified='".date('Y-m-d')."', ".(!empty($_REQUEST['delImg'])?$this->imageProcessing($idManuf,'del')."image='', ":"")."
                                                    title='".$title."',description='".$desc."',keywords='".$keyw."',
                                                    id_user='".$user."' WHERE id='".$idManuf."'");

                          if($image = $this->imageProcessing($idManuf))
                             $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_manufacturers SET image='".$image."' WHERE id= ".$idManuf."");
                  }

                  if(!empty($_REQUEST['apply']))
                      $this->common->redirect("index.php?action=edit_manufacturers&idManuf=".$idManuf);
                  else
                      $this->common->redirect("index.php?action=manufacturers");

        }

        function deleteProcessing($id){

                $this->imageProcessing($id,'del');
                @unlink($this->pdf_pass.$id.".pdf");
                $this->auth->db->Execute("DELETE FROM ".TBL_PREF."".$this->table." WHERE id='".$id."' ");
                if($this->table == 'catalog_products') $this->auth->db->Execute("DELETE FROM ".TBL_PREF."catalog_p2c WHERE id_product='".$id."' ");
        }

        function replaceProcessing($replace,$destination){


                   if($this->table == 'catalog_categories')
                     if($replace !== $destination)
                        $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_categories SET id_parent='".$destination."' WHERE id='".$replace."'");

                   if($this->table == 'catalog_products')
                        $this->auth->db->Execute("UPDATE ".TBL_PREF."catalog_p2c SET id_category='".$destination."' WHERE id_product='".$replace."'");


                   if(($replace !== $destination and $this->table == 'catalog_categories') or $this->table == 'catalog_products')
                       $href = "index.php?action=catalog&idCat=".$destination."&idPar=".$_REQUEST['parent_'.$destination]."&idPrev=".$_REQUEST['prev_'.$destination];
                   else
                       $href = "index.php?action=catalog&idCat=".$_REQUEST['parent_'.$replace]."&idPar=".$_REQUEST['parent_'.$_REQUEST['parent_'.$replace]]."&idPrev=".$_REQUEST['prev_'.$_REQUEST['parent_'.$replace]];
               $this->common->redirect($href);
        }

          function imageProcessing($galleryId, $mode=''){

               if(empty($mode)){
                 if(!empty($_FILES['userfile']['tmp_name'])){
                   switch($_FILES['userfile']['type']){
                      case "image/gif":
                      case "image/jpg":
                      case "image/jpeg":
                      case "image/pjpeg":

                            $small = $this->processingFilename($this->destination.'small_'.$galleryId.$_FILES['userfile']['type']);
                            $medium = $this->processingFilename($this->destination.'medium_'.$galleryId.$_FILES['userfile']['type']);
                            $original = $this->processingFilename($this->destination.$galleryId.$_FILES['userfile']['type']);

                            $this->imgResize($_FILES['userfile']['tmp_name'],$small,$this->s_w,$this->s_h);
                            $this->imgResize($_FILES['userfile']['tmp_name'],$medium,$this->m_w,$this->m_h);

                            if(move_uploaded_file($_FILES['userfile']['tmp_name'], $original)){

                               $filename = str_replace($this->destination, "", $original);
                               return $filename;

                               }
                      break;

                      default: echo "<br>Неверный формат файла: ".$_FILES['userfile']['type'];
                               return false;
                      break;
                      }
                 }
               }elseif($mode == 'del'){

                       $image = $this->auth->QueryExecute("SELECT ".TBL_PREF."image FROM ".$this->table." WHERE id='".$galleryId."'",0);

                       if(!empty($image[0])){

                           if(file_exists($this->destination.'small_'.$image[0])) unlink($this->destination.'small_'.$image[0]);
                           if(file_exists($this->destination.'medium_'.$image[0])) unlink($this->destination.'medium_'.$image[0]);
                           if(file_exists($this->destination.$image[0])) unlink($this->destination.$image[0]);
                       }
               }
          return false;
          }

    /*
      Назначение: Функция imgResize - создаёт копию исходного изображения в
                                      заданных размерах
      Параметры: $f_src (I) - имя файла источника
                 $f_dst (I) - имя файла назначения
                 $w_dst (I) - ширина создаваемого изображения
                 $h_dst (I) - высота создаваемого изображения
      Возвращает: true - е. изобр. успешно создаётся, и false в противном сл.
      Описание: создаёт копию изображения $f_src в пропорциях ширины исходного
                изображения к $w
    */
    function imgResize($f_src = '', $f_dst = '', $w_dst = 0, $h_dst = 0)
    {
      if(!empty($f_src) and !empty($f_dst) and !empty($w_dst) and !empty($h_dst))
        if(file_exists($f_src))
        {
          // получаем информацию о исходном изображении
          list($w_src, $h_src, $type, $attr) = getimagesize($f_src);

          // получаем отношения размеров, к. должны получить к исходным
          $h_proporc = $h_dst/$h_src;   $w_proporc = $w_dst/$w_src;

          $proporc = max($h_proporc, $w_proporc);       // опред. большую пропорц.

          //$h_dst = round(($w_dst/$w_src)*$h_src);       // высота нового изобр.

          $rsc_dst = ImageCreateTrueColor($w_dst, $h_dst)
                         or die ("Ошибка при создании изображения");

          // получаем ресурс источника из файла
          switch($type)                                 // по расширению файла
          {
            // gif
            case 1: $rsc_src = imagecreatefromgif($f_src);  break;
            // jpg
            case 2: $rsc_src = ImageCreateFromJpeg($f_src); break;
            // png
            case 3: $rsc_src = imagecreatefrompng($f_src);  break;

            default: return false;                      // другие форматы
          }

          // копирование изображения с изменением его размера
          if(!ImageCopyResampled($rsc_dst, $rsc_src, 0, 0,
                                 floor($w_src/2-($w_dst/$proporc)/2),
                                 floor($h_src/2-($h_dst/$proporc)/2),
                                 $w_dst, $h_dst,
                                 $w_dst/$proporc, $h_dst/$proporc)) return false;

          // сохраняем ресурс назначение в файл
          switch($type)                                 // по расширению файла
          {
            // gif
            case 1: return imagegif($rsc_dst, $f_dst);
            // jpg
            case 2: return imagejpeg($rsc_dst, $f_dst);
            // png
            case 3: return imagepng($rsc_dst, $f_dst);

            default: return false;                      // другие форматы
          }

        }

      return false;
    }

    function processingFilename($filename){

          $result = str_replace("jpeg", "jpg", str_replace("pjpeg", "jpg", str_replace("image/", ".", $filename)));
    return $result;
    }

    function getParent($id){

            $id_parent = $this->auth->QueryExecute("SELECT id_parent FROM ".TBL_PREF."catalog_categories WHERE id='".$id."'",0);
            $this->idPar = $id_parent[0];
    }

        function getManufacturersList($def=''){

                list($id,$name)=$this->auth->QueryExecute("SELECT id,name FROM ".TBL_PREF."catalog_manufacturers WHERE published='1' ORDER BY sort_order,name",array(0,1));
                if(is_array($id)) foreach($id as $k=>$v)
                $html .= "<option value='".$v."' ".(($v == $def)?'selected':'')." >".$name[$k]."</option>";

        return $html;
        }

        function getMAnufacturersName($idManuf){

                $name = $this->auth->QueryExecute("SELECT name FROM ".TBL_PREF."catalog_manufacturers WHERE id='".$idManuf."'",0);
        return $name[0];
        }

        function defineSortOrder($idCat=''){

                   if($this->table == 'catalog_categories')
                      $sql = "SELECT MAX(sort_order) as ord FROM ".TBL_PREF."catalog_categories WHERE id_parent='".$idCat."' ";

                   if($this->table == 'catalog_products')
                      $sql = "SELECT MAX(p.sort_order) as ord FROM ".TBL_PREF."catalog_products p LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product WHERE p2c.id_category='".$idCat."' ";

                   if($this->table == 'catalog_manufacturers')
                      $sql = "SELECT MAX(sort_order) as ord FROM ".TBL_PREF."catalog_manufacturers";

                   $order = $this->auth->QueryExecute($sql,0);
                   if(empty($order[0])) $result = 1;
                   else $result = $order[0]+1;

        return $result;
        }

        function categoryExists($name,$parId=''){

                 $result = $this->auth->QueryExecute("SELECT id FROM ".TBL_PREF."catalog_categories WHERE name='".$name."' AND id_parent='".$parId."'",0);
                 if(is_array($result) and !empty($result[0])) return true;
                 else return false;
        }


        function productExists($name,$idCat=''){

                 $result = $this->auth->QueryExecute("SELECT p.id FROM ".TBL_PREF."catalog_products p LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product WHERE p.name='".$name."' AND p2c.id_category='".$idCat."'",0);
                 if(is_array($result) and !empty($result[0])) return true;
                 else return false;
        }

        function manufacturersExists($name){

                 $result = $this->auth->QueryExecute("SELECT id FROM ".TBL_PREF."catalog_manufacturers WHERE name='".$name."'",0);
                 if(is_array($result) and !empty($result[0])) return true;
                 else return false;
        }

        function Lock($Id){

                 $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET published=0 WHERE id=".$Id." ");
        }

        function Unlock($Id){

                 $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET published=1 WHERE id=".$Id." ");
        }

        function MoveUp($Id,$idCat='',$sort_order){

                 if($sort_order>0){

                     if($this->table == 'catalog_categories')
                        $sql = "SELECT id, IFNULL(sort_order,0) as sort_order FROM ".TBL_PREF."catalog_categories WHERE sort_order<".$sort_order." AND id_parent='".$idCat."' ORDER BY sort_order DESC LIMIT 0,1";
                     if($this->table == 'catalog_products')
                        $sql = "SELECT p.id, IFNULL(p.sort_order,0) as sort_order FROM ".TBL_PREF."catalog_products p
                                LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                                WHERE p.sort_order<".$sort_order." AND p2c.id_category='".$idCat."' ORDER BY p.sort_order DESC LIMIT 0,1";
                     if($this->table == 'catalog_manufacturers')
                        $sql = "SELECT id, IFNULL(sort_order,0) as sort_order FROM ".TBL_PREF."catalog_manufacturers WHERE sort_order<".$sort_order." ORDER BY sort_order DESC LIMIT 0,1";


                     //print $sql;
                     list($id,$sort)=$this->auth->QueryExecute($sql, array(0,1));

                     $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET sort_order='".$sort_order."' WHERE id='".$id[0]."' ");
                     $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET sort_order='".$sort[0]."' WHERE id='".$Id."' ");
                 }
         }

         function MoveDown($Id,$idCat='',$sort_order=''){

                 if($this->table == 'catalog_categories'){
                    $r = "SELECT MAX(sort_order) as maxSort FROM ".TBL_PREF."catalog_categories WHERE id_parent='".$idCat."' ";
                    $sql = "SELECT id, IFNULL(sort_order,0) as sort_order FROM ".TBL_PREF."catalog_categories WHERE sort_order>".$sort_order." AND id_parent='".$idCat."' ORDER BY sort_order LIMIT 0,1";
                 }
                 if($this->table == 'catalog_products'){

                    $r = "SELECT MAX(p.sort_order) as maxSort FROM ".TBL_PREF."catalog_products p
                          LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                          WHERE p2c.id_category='".$idCat."'";

                    $sql = "SELECT p.id, IFNULL(p.sort_order,0) as sort_order FROM ".TBL_PREF."catalog_products p
                            LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id=p2c.id_product
                            WHERE p.sort_order>".$sort_order." AND p2c.id_category='".$idCat."' ORDER BY p.sort_order LIMIT 0,1";
                 }
                 if($this->table == 'catalog_manufacturers'){

                    $r = "SELECT MAX(sort_order) as maxSort FROM ".TBL_PREF."catalog_manufacturers";
                    $sql = "SELECT id, IFNULL(sort_order,0) as sort_order FROM ".TBL_PREF."catalog_manufacturers WHERE sort_order>".$sort_order." ORDER BY sort_order LIMIT 0,1";
                 }

                 $maxSort = $this->auth->QueryExecute($r,0);


                 if(empty($sort_order)) $sort_order = 0;
                 if($maxSort[0]>$sort_order){

                    list($id,$sort)=$this->auth->QueryExecute($sql, array(0,1));

                     $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET sort_order='".$sort_order."' WHERE id='".$id[0]."' ");
                     $this->auth->db->Execute("UPDATE ".TBL_PREF."".$this->table." SET sort_order='".$sort[0]."' WHERE id='".$Id."' ");
                 }
         }

         function getCategoryTree($tree=null, $parentID = 0, $level='',$block=''){

                if($parentID !== $block){
                $q = mysql_query("SELECT c1.id,c1.name,c1.id_parent, IFNULL(c2.id_parent,0) as idPrev
                                         FROM ".TBL_PREF."catalog_categories c1
                                         LEFT JOIN ".TBL_PREF."catalog_categories c2 ON c1.id_parent=c2.id
                                         WHERE c1.id_parent = ".$parentID." ORDER BY c1.sort_order");

                $level = $level.'&nbsp;|&nbsp;';

                while ($row = mysql_fetch_assoc($q)){

                   $tmp_i = count($tree);
                   $tree[] = $row;
                   $tree[$tmp_i]['level'] = $level;
                   $tree = $this->getCategoryTree($tree, $row['id'], $level,$block);
                }
           }
           return $tree;
         }

         function getCategoryList($onChange='',$def='',$block='',$action='',$replace=''){

                 $treeArray = $this->getCategoryTree(null,0,'',$block);

                 $html = '<form style="margin:0;" name="lister" method="post" action="?action='.$action.'&replace='.$replace.'">
                           '.(empty($action)?'Перейти в ':'').'<select name="destination" onChange="'.$onChange.'">
                           '.(!empty($action)?'<option value="">Выберите куда ходите переместить</option>':'').'
                           <option value="0">Начало</option>';

                 if(is_array($treeArray))
                 foreach($treeArray as $k=>$v) $html .= '<option value="'.$v['id'].'" '.(($v['id'] == $def)?'selected':'').' >'.$v['level'].$v['name'].'</option>';

                 $html .='</select>';


                     $html .= '<input name="parent_0" type="hidden" value="0"><input name="prev_0" type="hidden" value="0">';
                     if(is_array($treeArray))
                     foreach($treeArray as $k=>$v) $html .= '<input name="parent_'.$v['id'].'" type="hidden" value="'.$v['id_parent'].'"><input name="prev_'.$v['id'].'" type="hidden" value="'.$v['idPrev'].'">';

                 $html .='</form>';

         return $html;
         }

  }
?>