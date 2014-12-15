<?

  class loadModules
  {
          var $zip = null; var $mesage = null; var $arch = null; var $action = null; var $name = null;

          function __construct($workDir){

                   $this->workDir = $workDir;
          }

/* ������� ����������� ������� */
          function installModule(){

                   $this->zip->extract(PCLZIP_OPT_PATH,$this->workDir);  // ������������� ����� �� ��������� �����
                   $this->zip->delete(PCLZIP_OPT_BY_EREG, '(^[0-9a-z\_]*[\.]{1}php)');  // ������� ��� php ����� �� ������
                   $this->zip->delete(PCLZIP_OPT_BY_EREG, '(^[0-9a-z\_]*[\.]{1}css)');  // ������� ��� css ����� �� ������

                   $instructions = @file($this->workDir.'install.ini');  // ��������� ���������� install.ini

                   foreach($instructions as $k=>$v) if(!empty($v)){

                       $v = $this->stripScr($v);      //�������� ������� �������� ������ � �������� �������
                       if(strstr($v,'dir_')){

                          $dirData = $this->getDirFileData($v);
                          if(!@opendir($dirData['path'])) mkdir($dirData['path']);

                       }elseif(strstr($v,'file_')){

                          $fileData = $this->getDirFileData($v);
                             copy($this->workDir.$fileData['name'], $fileData['path']);
                             unlink($this->workDir.$fileData['name']);

                       }
                   }

                   if(file_exists($this->workDir.'install.php')) require_once($this->workDir.'install.php');

                   $this->zip->add($this->workDir.'uninstall.php',PCLZIP_OPT_REMOVE_PATH,$this->workDir); // ���������.��� ��������� � �����

                   @unlink($this->workDir.'install.php');          // ������� �������
                   @unlink($this->workDir.'uninstall.php');        // ������� ���������
                   @unlink($this->workDir.$modType['name'].'_style.css'); // ������� css
                   @unlink($this->workDir.'install.ini');            // ������� ���� ������������
          }

/* ������� ������������� ������*/
         function deleteModule(){

                $this->zip->extract(PCLZIP_OPT_PATH,$this->workDir);  // ������������� ����� �� ��������� �����
                $instructions = @file($this->workDir.'install.ini');  // ��������� ���������� install.ini


                for($i=count($instructions); $i>=0; $i--)
                if(!empty($instructions[$i])){

                       $v = $this->stripScr($instructions[$i]);   //�������� ������� �������� ������ � �������� �������
                       if(strstr($v,'dir_')){

                          $dirData = $this->getDirFileData($v);
                          $this->full_del_dir($dirData['path']);

                       }elseif(strstr($v,'file_')){

                          $fileData = $this->getDirFileData($v);
                          if(file_exists($fileData['path'])) unlink($fileData['path']);

                       }
                }

                if(file_exists($this->workDir.'uninstall.php')) include($this->workDir.'uninstall.php');

                @unlink($this->workDir.'uninstall.php');
                @unlink($this->workDir.'install.ini');            // ������� ���� ������������
                @unlink($this->workDir.$this->arch);              // ������� ������������ �����

         }

// ������� ����� � ���������� �������
         function full_del_dir ($directory){

                  $dir = opendir($directory);
                  while($file = readdir($dir))

                   if ( is_file ($directory."/".$file))  unlink ($directory."/".$file);

                  closedir ($dir);
                  rmdir ($directory);
         }

         function stripScr($string){

                  $v = str_replace(chr(015),'',$string);  // �������� ������ �������� �������
                  $v = str_replace(chr(012),'',$v);  // �������� ������ �������� ������
                  return $v;
         }


/* ��������������� ������� ��� ������� �������
   ���������� ������ � ������ �����\���������� � ����� ��� �����������\��������
*/
         function getDirFileData($array){

                 $data = explode(' ',$array);
                 $name = str_replace('mod_type=','',str_replace('file_name=','',str_replace('dir_name=','',$data[0])));
                 $path = str_replace('file_path=','',str_replace('dir_path=','',$data[1]));

                 $result = array('name'=>$name, 'path'=>$_SERVER['DOCUMENT_ROOT'].$path.$name);

                 return $result;
         }


/* ������ ������������� �������
*/
         function getModulesList(){

                 $m = opendir($this->workDir);

                 $html = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';

                 $c = 0;
                 while($modules = readdir($m))
                       if($modules !== '.' and $modules !== '..'){
                          $this->moduleName($modules);
                          $list .= '<tr '.((($c++)%2==0)?'class="dataTableRow1"':'').'>
                                     <td></td>
                                     <td><a href="?action='.$this->action.'">'.$this->name.'</a></td>
                                     <td class="tac"><a onClick="javascript: if(confirm(\'�� �������, ��� ������ ������� ������ '.$this->name.'?\n\r ������, ��� ��� �������� � ����������� ���� ������!\')) document.location.href=\'/c0ntr0lz0ne/index.php?action=del_modules&module='.$modules.'\'" href="javascript: void[0];">�������</a></td></tr>';
                       }
                 if(empty($list)) $list = '<tr><td colspan="3" class="tac"><b>��� ������������� �������</b></td></tr>';
                 else $head = '<th>&nbsp;</th><th class="tal">�������� ������:</th><th>��������:</th>';

                 $html .= $head.$list.'</table>';
                 closedir($m);

                 return $html;
         }

/* ��������� �������� ������ �� ������������
*/
         function moduleName($fileName){

                 switch($fileName){

                         case "news_module.zip": $this->name = '�������'; $this->action = 'news'; break;
                         case "catalog_module.zip": $this->name = '�������';  $this->action = 'catalog';  break;
                         case "gallery_module.zip": $this->name = '�������'; $this->action = 'gallery';  break;
                         case "banners_module.zip": $this->name = '�������'; $this->action = 'banners';  break;
                         default: $name = $fileName; break;
                 }
         }

/*  ��������� ������ �� ������� ���������
*/
    function isModuleInstall($moduleName)
    {
        switch ($moduleName)
        {
            case "news":    $r = (file_exists($this->workDir."news_module.zip")?1:0); break;
            case "gallery": $r = (file_exists($this->workDir."gallery_module.zip")?1:0); break;
            case "catalog": $r = (file_exists($this->workDir."catalog_module.zip")?1:0); break;
            case "banners": $r = (file_exists($this->workDir."banners_module.zip")?1:0); break;
        }

        if (!empty($r)) return true;
        else return false;
    }
  }
?>