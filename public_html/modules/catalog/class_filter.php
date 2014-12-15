<?
class catalog_filter
{
    var $disabled = null, $roots_list = '', $child_list = '',
        $I_max='', $Q_max='', $U='', $R='', $A='', $B='', $C='', $D='',
        $Drus='', $di='', $sealing_type='', $HT='', $parent = '';

    function __construct($auth, $idCat = '')
    {
        $this->auth  = $auth;
        $this->db    = $auth->db;
        $this->idCat = $idCat;
    }

/*  определяет предка страницы
*/
    function define_parent($idCat = '')
    {
        if (!empty($idCat)) $this->idCat = $idCat;

        $this->db->case_definer('catalog_categories');
        $parent = $this->db->get_data(array('id' => $this->idCat));

    return $parent[0]['id_parent'];
    }

/*  пределяет какие выпадающие меню показывать при навигации по страницам каталога
*/
    function define_lists($cat_prod)
    {
        $parent = $this->define_parent();

        if ($parent && $this->idCat)
        {
            $this->roots_list = $this->get_list(0, $cat_prod, $parent);
            $this->child_list = $this->get_list($parent, $cat_prod, $this->idCat);
        }
        elseif ($this->idCat)
        {
            $this->roots_list = $this->get_list(0, $cat_prod, $this->idCat);
            $this->child_list = $this->get_list($this->idCat, $cat_prod);
        }
        else
        {
            $this->roots_list = $this->get_list(0, $cat_prod);
            $this->child_list = $this->get_list(0, $cat_prod, '', '<>');
        }
    }

/*  Построение списка из массива
*/
    function build_list($data_array, $cat_prod, $default = '')
    {
        $list = '';
        if (is_array($data_array)) foreach ($data_array as $k => $v)
        {
            if ($cat_prod[$v['id']])
                $list .= '<option value="'.$v['id'].'" '.(($v['id'] == $default)?'selected':'').' >'.$v['name'].'</option>';
        }
    return $list;
    }

/*  получает данные для списка
*/
    function get_list($parent, $cat_prod, $default = '', $operator = '=')
    {
       $data = $this->db->get_data(array('id_parent' => $parent),'',$operator);
       $list = $this->build_list($data, $cat_prod, $default);

    return $list;
    }

/*  Фильтр диапазонов
*/
    function range_filter($min_array, $max_array, $min_def = '', $max_def = '')
    {
        /*
        $min_array = $max_array = null;

        switch ($case)
        {
            case "I_max":
            case "Q_max":
            case "R":

                $min_array = array('5' => 5, '10' => 10);
                $max_array = array('5' => 5, '10' => 10, '20' => 20);
            break;

            case "U":

                $min_array = array('1' => 1, '5' => 5);
                $max_array = array('1' => 1, '5' => 5, '10' => 10);;
            break;
        }
          */
    if (!$min_array[$min_def]) $min_array = array_merge(array($min_def => $min_def), $min_array);
    if (!$max_array[$max_def]) $max_array = array_merge($max_array, array($max_def => $max_def));

    return array($this->build_electro_list($min_array, $min_def),
                 $this->build_electro_list($max_array, $max_def));
    }

/*
*/
    function build_range_list($data, $def, $min = null, $max = null)
    {
        $list = '';
        if (is_array($data)) foreach ($data as $k => $v)
        {
            if (!empty($v))
            {
                if (isset($min))
                {
                    if ($v <= $min) $list .= $this->range_list_item($v, $k, $def);
                }
                elseif (isset($max))
                {
                    if ($v >= $max) $list .= $this->range_list_item($v, $k, $def);
                }
                else
                    $list .= $this->range_list_item($v, $k, $def);
            }
        }

    return $list;
    }

/*
*/
    function range_list_item($val, $atr, $def)
    {
    return  '<option value="'.$atr.'" '.(($atr == $def)?'selected':'').' >'.$val.'</option>';
    }

/*  Фильтр электрических параметров
*/
    function electro_filter($I_max='', $Q_max='', $U='', $R='', $A='', $B='', $C='', $D='', $Drus='', $di='', $sealing_type='', $HT='')
    {
        $filter = '';
        $help_ids = $this->get_help_id();

        // строим фильтры которые с диапазонами
        $data = $this->electro_filter_range_data();

        if (is_array($data)) foreach ($data as $k => $v )
        {
            $rf = $$v['name'];
            $filter .= '<tr><td width="33%"><img src="/img/help.gif" onClick="show_help(this, '.$help_ids[$v['name']].');" class="help_img">'.$v['title'].'</td>
                          <td>от</td>
                          <td><select onChange="list_processing(this);" size="1" name="'.$v['name'].'[]"  style="width: 47px">
                                <option value=""> --- </option>'.$rf[0].'
                              </select>
                          </td>
                          <td style="text-align: center">до</td>
                          <td><select onChange="list_processing(this);"  size="1" name="'.$v['name'].'[]" style="width: 47px">
                                <option value="">---</option>'.$rf[1].'
                              </select>
                          </td>
                        </tr>';
        }

        $filter .= '</table>
                    <p class="sub_head">Геометрические параметры</p>
                    <table width="100%" cellpadding="0" class="electro_filter">
                   ';

        // строим те, которые без диапазонов
        $data = $this->electro_filter_data();
        if (is_array($data)) foreach ($data as $k => $v)
        {
            if ($v['name'] == 'sealing_type')

                $filter .= '</table>
                            <p class="sub_head">Опции</p>
                            <table width="100%" cellpadding="0" class="electro_filter">
                           ';

            $filter .= '<tr><td width="33%"><img src="/img/help.gif" onClick="show_help(this, '.$help_ids[$v['name']].');" class="help_img" >'.$v['title'].'</td>
                            <td colspan="4"><select size="1" name="'.$v['name'].'" onChange="list_processing(this);">
                                  <option value=""> --- </option>'.$$v['name'].'
                                </select>
                            </td>
                        </tr>';
        }
        // поле с ценой
        /*
        $add .= '<tr>
                  <td>Цена</td>
                  <td colspan="4"><input name="price" type="text" value="" style="width: 119px"></td>
                </tr>';
          */
    return $filter.$add;
    }

    private function electro_filter_range_data()
    {
    return
    array(0 => array('title' => 'I<sub>max</sub> (A)',  'name' => 'I_max'),
          1 => array('title' => 'Q<sub>max</sub> (Вт)', 'name' => 'Q_max'),
          2 => array('title' => 'U (В)',  'name' => 'U'),
          3 => array('title' => 'R (Oм)', 'name' => 'R')
          );
    }

    private function electro_filter_data()
    {
    return
    array(5  => array('title' => 'А (мм)',         'name' => 'A'),
          6  => array('title' => 'В (мм)',         'name' => 'B'),
          7  => array('title' => 'C (мм)',         'name' => 'C'),
          8  => array('title' => 'D (мм)',         'name' => 'D'),
          9  => array('title' => '&Oslash;внеш.',  'name' => 'Drus'),
          10 => array('title' => '&Oslash;внутр.', 'name' => 'di'),
          11 => array('title' => 'Тип герм',       'name' => 'sealing_type'),
          12 => array('title' => 'HT',             'name' => 'HT'));
    }

    private function exclusion_regexp()
    {
    return '(\scart_prod\w+\s)|(\sPHPSESSID\s)|(\scategory_id\s)|(\scategory\s)|(\sproduct\s)|(\sparent_id\s)|(\scart_cat_id\s)|(\sx\s)|(\sy\s)|(\s__utm[\w]*\s)|(\sshoplogin\s)|(\sprice\s)|(\sfilter\s)|(\sfilter_data\s)|(\sfilter_search\s)|(\sclientID0\s)';
    }

    function get_help_id()
    {
    return array('name'  => 9,
                 'I_max' => 10,
                 'Q_max' => 11,
                 'U'     => 12,
                 'R'     => 13,
                 'A'     => 14,
                 'B'     => 15,
                 'C'     => 16,
                 'D'     => 17,
                 'H'     => 18,
                 'Drus'  => 19,
                 'd'     => 20,
                 'di'    => 20,
                 'sealing_type' => 21,
                 'HT'    => 22);
    }

/*  Получает значения для фильтров продукции
*/
    function electro_filter_values($array, $category_id = '')
    {
       $where_set = $id_set = '';
       $parents=$categories=$I_max=$Q_max=$U=$R=$A=$B=$C=$D=$Drus=$di=$sealing_type=$HT=null;

       if ($array['product']) $id_set .= $array['product'];
       elseif ($array['category'])
       {
           $id_data = $this->db->get_data(array('id_parent' => $array['category']),'catalog_categories');

           if (is_array($id_data)) foreach ($id_data as $k => $v)
               $id_set .= ','.$v['id'];
           // составляем строку запроса с id
           $id_set = substr($id_set,1);
       }

       if ($id_set) $where_set .= ' AND p2c.id_category IN ('.$id_set.') ';

       if (is_array($array)) foreach ($array as $k => $v)
           if (!preg_match('/^'.$this->exclusion_regexp().'/',' '.$k.' ') && !empty($v))

               if (is_array($v)) foreach ($v as $ke => $va)
               {
                   if (!empty($va))
                       $where_set .= ' AND p.'.(!empty($ke)?$k."<='".$va."'":$k.">='".$va."'");
               }
               else
                   $where_set .= ' AND p.'.$k.'= "'.$v.'"';

       $sql = "SELECT p.*, (1*p.price), c.id category_id, c.name category_name, c1.id par_id, c1.name par_name, c.pdf_file
               FROM ".TBL_PREF."catalog_products p
               LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id = p2c.id_product
               LEFT JOIN ".TBL_PREF."catalog_categories c ON p2c.id_category = c.id
               LEFT JOIN ".TBL_PREF."catalog_categories c1 ON c.id_parent = c1.id

               WHERE p.published = '1'".$where_set."
               GROUP BY p.id
               ORDER BY c1.name, c.name";

       //$this->test($sql);
       $q = $this->db->Execute($sql);
       while ($r = mysql_fetch_assoc($q))
       {
           $product_data[] = $r;

           $category[$r['par_id']]      = $r['par_name'];
           $products[$r['category_id']] = $r['category_name'];

           $I_max[$r['I_max']] = $r['I_max'];
           $Q_max[$r['Q_max']] = $r['Q_max'];
           $U[$r['U']] = $r['U'];
           $R[$r['R']] = $r['R'];
           $A[$r['A']] = $r['A'];
           $B[$r['B']] = $r['B'];
           $C[$r['C']] = $r['C'];
           $D[$r['D']] = $r['D'];
           $Drus[$r['Drus']] = $r['Drus'];
           $di[$r['di']] = $r['di'];
           $sealing_type[strtolower($r['sealing_type'])] = strtolower($r['sealing_type']);
           $HT[$r['HT']] = $r['HT'];
       }
       //array_multisort($category, SORT_ASC, SORT_NUMERIC);
       //array_multisort($products, SORT_ASC, SORT_NUMERIC);

      // $this->test($category);

       if (empty($this->roots_list))
           $this->roots_list = $this->build_electro_list($category, $array['category']);

       if (empty($this->child_list))
           $this->child_list = $this->build_electro_list($products, $array['product']);

       //$this->test($I_max);
       if (is_array($array)) foreach ($array as $k => $v)
       {
           if (!preg_match('/^'.$this->exclusion_regexp().'/',' '.$k.' ') && is_array($$k))
           {
               array_multisort($$k, SORT_ASC, (($k != 'sealing_type')?SORT_NUMERIC:SORT_STRING));

               if (is_array($v))

                   $this->$k = $this->range_filter($$k, $$k, $v[0], $v[1]);
               else
                   $this->$k = $this->build_electro_list($$k, $v);
           }
       }

    return $product_data;
    }

/*  Высняет большее и меньшее значение
*/
    function max_min($data)
    {
       if (is_array($data)) return array(min($data), max($data));
    }

/*  Пстроение списка электрофильтра
*/
    function build_electro_list($data_array, $default = '')
    {
        $list = '';
        if (is_array($data_array)) foreach ($data_array as $k => $v)
        {
            if (!empty($v))
            $list .= '<option value="'.$k.'" '.(($k == $default)?'selected':'').' >'.$v.'</option>';
        }

    return $list;
    }

    function get_where($array, $operator = '=', $ch1 = '', $ch2 = '')
    {
    return $this->db->get_where($array, $operator, $ch1, $ch2);
    }


    function test($value)
    {
        if (is_array($value))
        {
            print_r($value);
            print '<br /><br /><br />';
        }
        else
            print $value.'<br /><br /><br />';
    }

}
?>