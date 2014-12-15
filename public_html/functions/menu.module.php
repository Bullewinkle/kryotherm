<?
    /* ���������� ������ ���� �����
       $start - id ����, � �������� ��������� ������
       $limit - id ��� URL ����, �� �������� ��������� ��������� �����
       $mode  - �����
                2 - �������������� ����
                1 - ��������� ��������� ����
    */
    function TestTreeMenu($start=0,$limit='',$mode=1)
    {
        if(!is_numeric($limit))
        {
            $q = mysql_query("SELECT id FROM ".TBL_PREF."pages WHERE url='".$limit."'");
            $r = mysql_fetch_array($q);
            $limit = $r['id'];
        }

        // ����������� ��� ������ �� ����
        $q = mysql_query(" SELECT * FROM ".TBL_PREF."pages WHERE published=1 ORDER BY id_parent,sort_index");

        // ���������� ������ � ������
        while($r = mysql_fetch_assoc($q)) $db_data[] = $r;

        // �������� ������ � ��������, ������� � ������� $start
        $tree = recursion($db_data,$start);

        switch($mode)
        {
            // �������������� ����
            case "2":

                $rootsArray = findingRoots($tree, $limit, $start);
                $result = dropDownMenu($tree, $rootsArray, $start);

                break;

            // �� ��������� ���������� ��������� ������
            default: $result = $tree; break;
        }

    return $result;
    }

    /* ��������� ������ ��� ��������������� ����
       $array - ������ � �������
       $rootsArray - ������ � ��������� ��������� �����
       $start - id_parent ������ ����
    */
    function dropDownMenu($array, $rootsArray, $start)
    {
        if (is_array($array)) foreach($array as $k=>$v)
        {
            // ���������� �����,
            // ��� �����, ������� ����� ���� ��������� � �������� ������ �������
            // ��� ��������� �����
            if(($v['id_parent'] == $start && !in_array($v['id'], $rootsArray)) ||
                in_array($v['id_parent'], $rootsArray) ||
                in_array($v['id'],$rootsArray))

                $result[] = $v;
        }

    return $result;
    }


    /* ���������� ������ ������� id �� �������� ����� � �������� ��������
       $array - ������ ������
       $start - ��������� �����
       $limit - �������
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

    /*  ���������� ��������������� �� ������� (� ������ ��������) ������ (������)
        $array - �������������� ������
        $start - ��������� �����
    */
    function recursion($array,$start=0,$result=null)
    {
        if (is_array($array)) foreach($array as $k=>$v)
        {
            if($v['id_parent'] == $start && $v['published'] == 1)
            {
                $result[] = $v;
                $id = $v['id'];
                unset($array[$k]); // ������� ���������� � ��������� ������� �������, ����� ��������� �������� ���� ������

                $result = recursion($array, $id, $result);

            }else unset($array[$k]); // ������� ��������, ������� ���������� ���� ��������� ����� ��������
        }

    return $result;
    }
?>