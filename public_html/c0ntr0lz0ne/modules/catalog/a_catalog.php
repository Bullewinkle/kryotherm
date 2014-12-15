<?
  $auth->Authorizate();
   $action = (!empty($_REQUEST['action'])?$_REQUEST['action']:"");
   $idPrev = (!empty($_REQUEST['idPrev'])?$_REQUEST['idPrev']:0);
   $idCat = (!empty($_REQUEST['idCat'])?$_REQUEST['idCat']:0);
   $idPar = (!empty($_REQUEST['idPar'])?$_REQUEST['idPar']:0);
   $idProd = (!empty($_REQUEST['idProd'])?$_REQUEST['idProd']:0);
   $idManuf = (!empty($_REQUEST['idManuf'])?$_REQUEST['idManuf']:0);
   $lock = $_REQUEST['lock'];
   $delete = $_REQUEST['delete'];
   $move = $_REQUEST['move'];
   $sort_order = $_REQUEST['sort_order'];
   $replace = $_REQUEST['replace'];
   $destination = $_REQUEST['destination'];

   $catalog->getParent($idPrev);

   switch($action){

           case"add_category":case"insert_category":case"edit_category":case"update_category":case"delete_category":
           case"lock_category":case"unlock_category":case"moveUp_category":case"moveDown_category":
           case"replace_category":
                $catalog->caseDefiner('category');
                break;

           case"add_product":case"insert_product":case"edit_product":case"update_product":case"delete_product":
           case"lock_product":case"unlock_product":case"moveUp_product":case"moveDown_product":
           case"replace_product":
                $catalog->caseDefiner('product');
                break;

           case"add_manufacturers":case"insert_manufacturers":case"edit_manufacturers":case"update_manufacturers":case"delete_manufacturers":
           case"lock_manufacturers":case"unlock_manufacturers":case"moveUp_manufacturers":case"moveDown_manufacturers":
                $catalog->caseDefiner('manufacturers');
                break;
   }

   if($action == 'insert_category') $catalog->processingCategoryForm('insert',$idCat,$idPar,$idPrev);
   if($action == 'update_category') $catalog->processingCategoryForm('update',$idCat,$idPar,$idPrev);
   if($action == 'insert_product')  $catalog->processingProductsForm('insert',$idProd,$idCat,$idPar,$idPrev);
   if($action == 'update_product')  $catalog->processingProductsForm('update',$idProd,$idCat,$idPar,$idPrev);
   if($action == 'insert_manufacturers')  $catalog->processingManufacturersForm('insert',$idManuf);
   if($action == 'update_manufacturers')  $catalog->processingManufacturersForm('update',$idManuf);

   if($action == 'lock_category' or $action == 'lock_product' or $action == 'lock_manufacturers')   $catalog->Lock($lock);
   if($action == 'unlock_category' or $action == 'unlock_product' or $action == 'unlock_manufacturers') $catalog->Unlock($lock);
   if($action == 'moveUp_category' or $action == 'moveUp_product'or $action == 'moveUp_manufacturers') $catalog->MoveUp($move,$idCat,$sort_order);
   if($action == 'moveDown_category' or $action == 'moveDown_product'or $action == 'moveDown_manufacturers') $catalog->MoveDown($move,$idCat,$sort_order);

   if($action == 'replace_category' or $action == 'replace_product') $catalog->replaceProcessing($replace,$destination);

   if($action == 'delete_product' || $action == 'delete_category' || $action == 'delete_manufacturers') $catalog->deleteProcessing($delete);

   if(($action == 'lock_product' or $action == 'unlock_product' or $action == 'delete_product') and !empty($idManuf))
       $common->redirect("index.php?action=manufacturers&idManuf=".$idManuf);

if(($action == 'catalog' or $action == 'unlock_category' or $action == 'lock_category' or $action == 'unlock_product' or $action == 'lock_product' or $action == 'delete_category' or $action == 'delete_product' or $action == 'moveUp_category' or $action == 'moveUp_product' or $action == 'moveDown_category' or $action == 'moveDown_product') and empty($idManuf)){
?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top width="100%">
                      <h1>Управление каталогом</h1>

                      <?=$catalog->getCategoryList("javascript: var idCat = this.value ;
                                                              var idPar = eval('document.lister.parent_'+idCat+'.value');
                                                              var idPrev = eval('document.lister.prev_'+idCat+'.value');
                                                              document.location.href='index.php?action=catalog&idCat='+idCat+'&idPar='+idPar+'&idPrev='+idPrev;",$idCat);?>
                    </td>
                  </tr>
                </table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
                   <tr>
                    <td valign="top" style="padding-right: 20px;">
                      <div style="background: transparent url(images/points.gif) repeat-x scroll center top; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: 100%; margin-top: 10px;">&nbsp;</div>
                      <div id="dataTable"><?=$catalog->getCatalog($idCat,$idPar,$idPrev);?></div>
                    </td>
                   </tr>
                   <tr>
                    <td align="right" style="padding: 20px 20px 20px 0;" >
<? if(!empty($idCat)){ ?><a href="?action=catalog&idCat=<?=$idPar;?>&idPar=<?=$idPrev;?>&idPrev=<?=$catalog->idPar;?>"><img border="0" src="images/icons/button-back.gif"></a><? } ?>
                         <a href="?action=add_category&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>"><img border="0" src="images/icons/add_category.gif"></a>
                         <a href="?action=add_product&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>"><img border="0" src="images/icons/add_product.gif"></a>
                    </td>
                   </tr>
                  </table>

<? }elseif($action == 'add_category'){ ?>

<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=insert_category" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Добавление категории</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogCategoryForm('add',$idCat,$idPar,$idPrev);?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type="hidden" value="<?=$idCat;?>" name="idCat">
          <input type="hidden" value="<?=$idPar;?>" name="idPar">
          <input type="hidden" value="<?=$idPrev;?>" name="idPrev">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>

<? }elseif($action == 'edit_category'){ ?>

<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=update_category" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Редактирование категории</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idPar;?>&idPar=<?=$idPrev;?>&idPrev=<?=$catalog->idPar;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogCategoryForm('edit',$idCat,$idPar,$idPrev);?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type="hidden" value="<?=$idCat;?>" name="idCat">
          <input type="hidden" value="<?=$idPar;?>" name="idPar">
          <input type="hidden" value="<?=$idPrev;?>" name="idPrev">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idPar;?>&idPar=<?=$idPrev;?>&idPrev=<?=$catalog->idPar;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>

<? }elseif($action == 'add_product'){ ?>
<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=insert_product" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Добавление нового товара</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogProductForm('add',$idCat,$idPar);?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type="hidden" value="<?=$idProd;?>" name="idProd" >
          <input type="hidden" value="<?=$idCat;?>" name="idCat">
          <input type="hidden" value="<?=$idPar;?>" name="idPar">
          <input type="hidden" value="<?=$idPrev;?>" name="idPrev">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=catalog&idCat=<?=$idCat;?>&idPar=<?=$idPar;?>&idPrev=<?=$idPrev;?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>

<? }elseif($action == 'edit_product'){ ?>
<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=update_product" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Редактирование товара</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="<?=(!empty($idManuf)?'?action=manufacturers&idManuf='.$idManuf:'?action=catalog&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev)?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogProductForm('edit',$idProd);?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type="hidden" value="<?=$idProd;?>" name="idProd" >
          <input type="hidden" value="<?=$idCat;?>" name="idCat">
          <input type="hidden" value="<?=$idPar;?>" name="idPar">
          <input type="hidden" value="<?=$idPrev;?>" name="idPrev">
          <input name="idManuf" type="hidden" value="<?=$idManuf;?>">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="<?=(!empty($idManuf)?'?action=manufacturers&idManuf='.$idManuf:'?action=catalog&idCat='.$idCat.'&idPar='.$idPar.'&idPrev='.$idPrev)?>" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>

<? }elseif($action == 'manufacturers' or $action == 'lock_manufacturers' or  $action == 'unlock_manufacturers' or $action == 'moveUp_manufacturers' or $action == 'moveDown_manufacturers' or $action == 'delete_manufacturers'){ ?>
<? if(empty($idManuf)){ ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top width="100%">
                      <h1>Управление производителями</h1>
                    </td>
                  </tr>
                </table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
                   <tr>
                    <td valign="top" style="padding-right: 20px;">
                      <div style="background: transparent url(images/points.gif) repeat-x scroll center top; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: 100%; margin-top: 10px;">&nbsp;</div>
                      <div id="dataTable"><?=$catalog->getManufacturers();?></div>
                    </td>
                   </tr>
                   <tr>
                    <td align="right" style="padding: 20px 20px 20px 0;" >
                         <a href="?action=add_manufacturers"><img border="0" src="images/icons/add.gif"></a>
                    </td>
                   </tr>
                  </table>
<? }else{ ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
                    <td align="left" valign=top width="100%">
                      <h1>Список товаров от "<?=$catalog->getMAnufacturersName($idManuf);?>"</h1>
                      <form action="?action=manufacturers&idManuf=<?=$idManuf;?>" method="post">
                       Поиск товара: <input name="find" type="text" value="<?=$_REQUEST['find']?>"><input type="submit" value="Найти!">
                      </form>
                    </td>
                  </tr>
                </table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
                   <tr>
                    <td valign="top" style="padding-right: 20px;">
                      <div style="background: transparent url(images/points.gif) repeat-x scroll center top; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: 100%; margin-top: 10px;">&nbsp;</div>
                      <div id="dataTable"><?=$catalog->getProductsList($idManuf);?></div>
                    </td>
                   </tr>
                   <tr>
                    <td align="right" style="padding: 20px 20px 20px 0;" >
                         <a href="?action=manufacturers"><img border="0" src="images/icons/button-back.gif"></a>
                    </td>
                   </tr>
                  </table>
<? } ?>
<? }elseif($action == 'add_manufacturers'){ ?>

<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=insert_manufacturers" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Новый производитель</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=manufacturers" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogManufacturersForm('insert');?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=manufacturers" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>
<? }elseif($action == 'edit_manufacturers'){ ?>

<table width="100%">
<tr><td style="padding-right: 20px;">
<form name="category" enctype="multipart/form-data" action="?action=update_manufacturers" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/catalog-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Редактирование производителя</h1>
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=manufacturers" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 <tr><td colspan="3"><?=$catalog->CatalogManufacturersForm('edit',$idManuf);?></td></tr>
 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input name="idManuf" type="hidden" value="<?=$idManuf;?>">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">
        <div class="button2"><a href="javascript:void[0];" onClick="category.apply.value='1'; category.submit();" title="">Применить</a></div>
        <div class="button2"><a href="?action=manufacturers" title="">Отменить</a></div>
        <div class="button2"><a href="javascript:void[0];" onClick="category.submit();" title="">Сохранить</a></div>
        </td></tr>
</table>
</td></tr></table>
<? } ?>