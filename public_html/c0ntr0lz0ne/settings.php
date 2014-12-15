<?
$auth->Authorizate();

   $case = (!empty($_REQUEST['case'])?$_REQUEST['case']:'');
   $constId = (!empty($_REQUEST['constId'])?$_REQUEST['constId']:'');
   $constName = (!empty($_REQUEST['constName'])?$common->prepareString($_REQUEST['constName']):'');
   $constDescr =(!empty($_REQUEST['constDescr'])?$common->prepareString($_REQUEST['constDescr']):'');
   $constVal = $common->prepareString($_REQUEST['parameter'].$_REQUEST['constVal']);
   $mod = (!empty($_REQUEST['mod'])?$_REQUEST['mod']:'');


   if (!empty($case)) $settings->caseDefiner($case);

   if (empty($constId))
   {
       if (!empty($constName))
       {
           if (!$settings->isConstantExist($constName))
           {
               $auth->db->Execute("INSERT INTO ".TBL_PREF."settings SET constant='".$constName."', description='".$constDescr."', value='".$constVal."', kind='".$settings->kind."'");
               $settings->createConfig();
           }
       }
   }else
   {
       if ($mod == 'update')
       {
           if (!empty($constName))
           {
               //if ($constName == 'TBL_PREF')
               //    $newTblPref = $settings->tableRenamer($constVal, $constId);

               $auth->db->Execute("UPDATE ".(isset($newTblPref)?$newTblPref:TBL_PREF)."settings
                                  SET constant='".$constName."',
                                      description='".$constDescr."',
                                      value='".$constVal."',
                                      kind='".$settings->kind."'
                                  WHERE id='".$constId."'");

               $settings->createConfig($newTblPref);
               $common->redirect("/c0ntr0lz0ne/index.php?action=settings&case=".$case);
           }
       }
       elseif ($mod == 'edit')
       {
           list ($id,$name,$descr,$value) =
           $auth->QueryExecute("SELECT * FROM ".TBL_PREF."settings WHERE id='".$constId."'",array(0,1,2,3));

           if(strchr($value[0], '\'.$_SERVER[\'DOCUMENT_ROOT\'].\'/')) $addition = 1;
           elseif(strchr($value[0], 'http://\'.$_SERVER[\'SERVER_NAME\'].\'/')) $addition = 2;
           else $addition = null;

           $value[0] = str_replace('http://\'.$_SERVER[\'SERVER_NAME\'].\'/','',$value[0]);
           $value[0] = str_replace('\'.$_SERVER[\'DOCUMENT_ROOT\'].\'/','',$value[0]);

           $mod = 'update';
       }
       elseif($mod == 'del')
       {
           $auth->db->Execute("DELETE FROM ".TBL_PREF."settings WHERE id='".$constId."'");
           $settings->createConfig();
           $common->redirect("/c0ntr0lz0ne/index.php?action=settings&case=".$case);
       }
   }
?>

<table width="100%">
<tr><td style="padding-right: 20px;">
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="editor">
 <tr>
  <td width=150><img src="images/settings-m.jpg" width=120 height=120></td>
  <td align="left" valign=top width="100%">
      <h1>Настройки</h1>
        <div <?=(($case == 'common')?'class="button3"':'class="button2"')?> ><a href="?action=settings&case=common" title="">Общие</a></div>
        <div <?=(($case == 'pages')?'class="button3"':'class="button2"')?> ><a href="?action=settings&case=pages" title="">Страницы</a></div>

<? if($modules->isModuleInstall('news')){ ?> <div <?=(($case == 'news')?'class="button3"':'class="button2"')?><a href="?action=settings&case=news" title="">Новости</a></div> <? } ?>
<? if($modules->isModuleInstall('catalog')){ ?> <div <?=(($case == 'catalog')?'class="button3"':'class="button2"')?><a href="?action=settings&case=catalog" title="">Каталог</a></div> <? } ?>
<? if($modules->isModuleInstall('gallery')){ ?> <div <?=(($case == 'gallery')?'class="button3"':'class="button2"')?><a href="?action=settings&case=gallery" title="">Галерея</a></div> <? } ?>
<? if($modules->isModuleInstall('banners')){ ?> <div <?=(($case == 'banners')?'class="button3"':'class="button2"')?><a href="?action=settings&case=banners" title="">Баннеры</a></div> <? } ?>
  </td>
 </tr>
 <tr><td height="5" colspan="3"><div style="background: transparent url(images/points.gif) repeat-x scroll center top; height: 5px; width: 100%;">&nbsp;</div></td></tr>
 <!--
 <tr id="dataTable"><th colspan="3">&nbsp;</th></tr>
 <tr><td colspan="3">&nbsp;</td></tr>
 -->

 <tr><td colspan="3">
<form name="settings" action="/c0ntr0lz0ne/index.php?action=settings&case=<?=$case;?><?=(!empty($mod)?'&mod='.$mod:'')?><?=(!empty($constId)?'&constId='.$constId:'')?>" method="post">
  <table width="100%" class="settings" id="dataTable">
   <tr><th class="tal" >Имя константы</th><th width="33%">Описание</th>
       <th>Значение</th>
       <th width="200">
           <input name="parameter" type="radio" value="" <?=(empty($addition)?'checked':'')?> >NONE &nbsp;
           <input name="parameter" type="radio" value="'.$_SERVER['DOCUMENT_ROOT'].'/" <?=(($addition == 1)?'checked':'')?> >PAHT &nbsp;
           <input name="parameter" type="radio" value="http://'.$_SERVER['SERVER_NAME'].'/" <?=(($addition == 2)?'checked':'')?> >URL
       </th>
       <th colspan="2">Действия</th></tr>

   <tr><td><input style="width: 100%" name="constName" type="text" value="<?=$name[0];?>"></td>
       <td><input style="width: 100%" name="constDescr" type="text" value="<?=$descr[0];?>"></td>
       <td colspan="2"><input style="width: 100%" name="constVal" type="text" value="<?=$value[0];?>"></td>
       <td class="tac" colspan="2"><input type="Submit" value="Применить"></td>

   <?=$settings->constantsList($case);?>
  </table>
  </form>
 </td></tr>


 <tr bgcolor="#c0c0c0"><td colspan="3"><img width="1" height="1" border="0" src="../images/blank.gif"></td></tr>
 <tr><td align="center" colspan="3">
          <input type="hidden" value="<?=$galleryId;?>" name="galleryId">
          <input type="hidden" value="<?=$imageId;?>" name="imageId">
          <input type=hidden name=apply value="">
     </td>
 </tr>
 <tr>
  <td></td>
  <td colspan="2">&nbsp;</td></tr>
</table>
</td></tr></table>