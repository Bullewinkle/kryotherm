<?

  class loadModules
  {
          var $zip = null; var $mesage = null; var $arch = null; var $action = null; var $name = null;

          function __construct($workDir){

                   $this->workDir = $workDir;
          }

/* функция инсталирует модульи */
          function installModule(){

                   $this->zip->extract(PCLZIP_OPT_PATH,$this->workDir);  // распаковываем архив во временную папку
                   $this->zip->delete(PCLZIP_OPT_BY_EREG, '(^[0-9a-z\_]*[\.]{1}php)');  // удаляем все php файлы из архива
                   $this->zip->delete(PCLZIP_OPT_BY_EREG, '(^[0-9a-z\_]*[\.]{1}css)');  // удаляем все css файлы из архива

                   $instructions = @file($this->workDir.'install.ini');  // считываем содержание install.ini

                   foreach($instructions as $k=>$v) if(!empty($v)){

                       $v = $this->stripScr($v);      //вырезаем символы переноса строки и возврата каретки
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

                   $this->zip->add($this->workDir.'uninstall.php',PCLZIP_OPT_REMOVE_PATH,$this->workDir); // анинсталл.пхп добавляем в архив

                   @unlink($this->workDir.'install.php');          // удаляем инсталл
                   @unlink($this->workDir.'uninstall.php');        // удаляем анинсталл
                   @unlink($this->workDir.$modType['name'].'_style.css'); // удаляем css
                   @unlink($this->workDir.'install.ini');            // удаляем файл конфигурации
          }

/* Функция деинсталирует модули*/
         function deleteModule(){

                $this->zip->extract(PCLZIP_OPT_PATH,$this->workDir);  // распаковываем архив во временную папку
                $instructions = @file($this->workDir.'install.ini');  // считываем содержание install.ini


                for($i=count($instructions); $i>=0; $i--)
                if(!empty($instructions[$i])){

                       $v = $this->stripScr($instructions[$i]);   //вырезаем символы переноса строки и возврата каретки
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
                @unlink($this->workDir.'install.ini');            // удаляем файл конфигурации
                @unlink($this->workDir.$this->arch);              // удаляем установочный архив

         }

// удаляет папку с вложенными файлами
         function full_del_dir ($directory){

                  $dir = opendir($directory);
                  while($file = readdir($dir))

                   if ( is_file ($directory."/".$file))  unlink ($directory."/".$file);

                  closedir ($dir);
                  rmdir ($directory);
         }

         function stripScr($string){

                  $v = str_replace(chr(015),'',$string);  // вырезаем символ перевода коретки
                  $v = str_replace(chr(012),'',$v);  // вырезаем символ переноса строки
                  return $v;
         }


/* вспомогательная функция для парсера конфига
   возвращает массив с именем файла\директории и путем для копирования\создания
*/
         function getDirFileData($array){

                 $data = explode(' ',$array);
                 $name = str_replace('mod_type=','',str_replace('file_name=','',str_replace('dir_name=','',$data[0])));
                 $path = str_replace('file_path=','',str_replace('dir_path=','',$data[1]));

                 $result = array('name'=>$name, 'path'=>$_SERVER['DOCUMENT_ROOT'].$path.$name);

                 return $result;
         }


/* Список установленных модулей
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
                                     <td class="tac"><a onClick="javascript: if(confirm(\'Вы уверены, что хотите удалить модуль '.$this->name.'?\n\r Учтите, что это приведет к уничтожению всех данных!\')) document.location.href=\'/c0ntr0lz0ne/index.php?action=del_modules&module='.$modules.'\'" href="javascript: void[0];">удалить</a></td></tr>';
                       }
                 if(empty($list)) $list = '<tr><td colspan="3" class="tac"><b>Нет установленных модулей</b></td></tr>';
                 else $head = '<th>&nbsp;</th><th class="tal">Название модуля:</th><th>Операции:</th>';

                 $html .= $head.$list.'</table>';
                 closedir($m);

                 return $html;
         }

/* переводит названия файлов на человеческий
*/
         function moduleName($fileName){

                 switch($fileName){

                         case "news_module.zip": $this->name = 'Новости'; $this->action = 'news'; break;
                         case "catalog_module.zip": $this->name = 'Каталог';  $this->action = 'catalog';  break;
                         case "gallery_module.zip": $this->name = 'Галерея'; $this->action = 'gallery';  break;
                         case "banners_module.zip": $this->name = 'Баннеры'; $this->action = 'banners';  break;
                         default: $name = $fileName; break;
                 }
         }

/*  проверяет модуль на предмет установки
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