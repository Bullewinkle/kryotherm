<?
    /* Возвращает массив меню сайта
       $start - id узла, с которого строиться дерево
       $limit - id или URL узла, до которого строиться раскрытая ветка
       $mode  - режим
                2 - раскрывающееся меню
                1 - раскрытое полностью меню
    */
    function TestTreeMenu($start=0,$limit='',$mode=1)
    {
        if(!is_numeric($limit))
        {
            $q = mysql_query("SELECT id FROM ".TBL_PREF."pages WHERE url='".$limit."'");
            $r = mysql_fetch_array($q);
            $limit = $r['id'];
        }

        // запрашиваем все данные из базы
        $q = mysql_query(" SELECT * FROM ".TBL_PREF."pages WHERE published=1 ORDER BY id_parent,sort_index");

        // записываем данные в массив
        while($r = mysql_fetch_assoc($q)) $db_data[] = $r;

        // получаем дерево в рекурсии, начиная с вершины $start
        $tree = recursion($db_data,$start);

        switch($mode)
        {
            // раскрывающееся меню
            case "2":

                $rootsArray = findingRoots($tree, $limit, $start);
                $result = dropDownMenu($tree, $rootsArray, $start);

                break;

            // по умолчанию возвращаем раскрытое дерево
            default: $result = $tree; break;
        }

    return $result;
    }

    /* Формирует массив для раскрывающегося меню
       $array - массив с данными
       $rootsArray - массив с вершинами раскрытой ветки
       $start - id_parent ствола меню
    */
    function dropDownMenu($array, $rootsArray, $start)
    {
        if (is_array($array)) foreach($array as $k=>$v)
        {
            // записываем ствол,
            // или ветки, которые лежат выше раскрытой в пределах данной вершины
            // или раскрытую ветку
            if(($v['id_parent'] == $start && !in_array($v['id'], $rootsArray)) ||
                in_array($v['id_parent'], $rootsArray) ||
                in_array($v['id'],$rootsArray))

                $result[] = $v;
        }

    return $result;
    }


    /* возвращает массив узловых id от заданной точки в обратной рекурсии
       $array - массив данных
       $start - начальная точка
       $limit - вершина
    */
    function findingRoots($array, $start, $limit, $result=array())
    {
        if($start>$limit)
            if (is_array($array)) foreach($array as $k=>$v)
            {
                if($v['id'] == $start)
                {
                    $result[] = $v['id'];
                    $result = findingRoots($array, $v['id_parent'], $limit, $result);
                }
            }

    return $result;
    }

    /*  Возвращает отсортированное по уровням (в прямой рекурсии) дерево (массив)
        $array - несортированый массив
        $start - начальная точка
    */
    function recursion($array,$start=0,$result=null)
    {
        if (is_array($array)) foreach($array as $k=>$v)
        {
            if($v['id_parent'] == $start && $v['published'] == 1)
            {
                $result[] = $v;
                $id = $v['id'];
                unset($array[$k]); // удаляем записанный в результат элемент массива, чтобы следующая итерация была короче

                $result = recursion($array, $id, $result);

            }else unset($array[$k]); // удаляем значения, которые находяться выше стартовой точки рекурсии
        }

    return $result;
    }
?>