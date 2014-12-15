<?
class Settings
{
    var $kind = null; var $case = null;

    function __construct($auth)
    {
        $this->auth = $auth;
    }

    function caseDefiner($case)
    {
        switch($case)
        {
            case "pages": $this->kind = 'pages'; $this->case = 'pages'; break;
            case "news": $this->kind = 'news'; $this->case = 'news'; break;
            case "gallery": $this->kind = 'gallery'; $this->case = 'gallery'; break;
            case "catalog": $this->kind = 'catalog'; $this->case = 'catalog'; break;
            case "banners": $this->kind = 'banners'; $this->case = 'banners'; break;
            case "common": $this->kind = 'common'; $this->case = 'common'; break;
        }
    }

    function constantsList()
    {
        list ($id,$constant,$descr,$value)=
        $this->auth->queryExecute("SELECT * FROM ".TBL_PREF."settings WHERE kind='".$this->kind."' ORDER BY constant",array(0,1,2,3));

        if (is_array($id))
        {

            foreach ($id as $k=>$v)

                $html .= '<tr '.(($k%2==0)?'class="dataTableRow1"':'').'>
                              <td>'.$constant[$k].'</td>
                              <td>'.$descr[$k].'</td>
                              <td colspan="2">'.str_replace('http://\'.$_SERVER[\'SERVER_NAME\'].\'/','http://'.$_SERVER['SERVER_NAME'].'/',str_replace('\'.$_SERVER[\'DOCUMENT_ROOT\'].\'/',$_SERVER['DOCUMENT_ROOT'].'/',$value[$k])).'</td>
                              <td class="tac"><a title="Редактровать" href="?action=settings&case='.$this->case.'&constId='.$v.'&mod=edit"><img width="22" height="22" border="0" alt="Редактировать" src="images/icons/ico_edit.gif"></a></td>
                              <td class="tac"><a onclick="if(confirm(\'Вы действительно хотите удалить '.$constant[$k].' ?\')) document.location.href=\'?action=settings&case='.$this->case.'&constId='.$v.'&mod=del\'" title="Удалить" href="javascript:void[0];"><img width="22" height="22" border="0" alt="Удалить" src="images/icons/ico_del.gif"></a></td>';
        }
        else $html = '<tr class="dataTableRow1"><td colspan="6" class="tac"> <b>Нет констант</b> </td></tr>';

    return $html;
    }

    function isConstantExist($constName)
    {
        $constId = $this->auth->QueryExecute("SELECT id FROM ".TBL_PREF."settings WHERE constant='".$constName."'",0);
        if(!empty($constId[0])) return true;
        else return false;
    }

    function createConfig($newTblPref=null)
    {
        $file = fopen( $_SERVER['DOCUMENT_ROOT'].'/settings/config.php', "wt");

         // print TBL_PREF;
       // print (isset($newTblPref)?$newTblPref:TBL_PREF);
       // exit();
        list($id, $name, $descr, $value) =
        $this->auth->QueryExecute("SELECT *
                                   FROM ".(isset($newTblPref)?$newTblPref:TBL_PREF)."settings
                                   ORDER by kind",array(0,1,2,3));

        fputs($file,'<?');

        foreach($id as $k=>$v)
            fputs($file,'define(\''.$name[$k].'\',\''.$value[$k].'\');'."\r\n");

        fputs($file,'?>');
        fclose($file);
    }

    function tableRenamer($constVal,$constId)
    {
        // список существующих таблиц в админке
        $tables = $this->auth->QueryExecute("show tables",0);

        // предидущий префикс таблиц
        $prevVal =
        $this->auth->QueryExecute("SELECT value
                                   FROM ".TBL_PREF."settings
                                   WHERE id='".$constId."'",0);

        // Обходим все таблицы и переименовываем их
        if ($constVal !== $prevVal[0])
        {
            foreach ($tables as $k=>$v)
            {
                $this->auth->db->Execute("RENAME TABLE
                                             ".$v."
                                          TO
                                              ".(!empty($prevVal[0])?str_replace($prevVal[0], $constVal, $v):$constVal.$v)."");
            }
        }

    return $constVal;
    }
}

?>