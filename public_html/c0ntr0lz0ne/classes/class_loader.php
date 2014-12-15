<?
class loader
{
    var $error = '', $preload = '', $header = '', $counter = '';

    function __construct($auth, $common)
    {
        $this->auth   = $auth;
        $this->db     = $auth->db;
        $this->common = $common;
    }

    function parser($file)
    {
        $data       = $this->read_file($file);
        $data_descr = $this->data_description();
        $reg_exp    = $this->reg_exp();
        $parsed_data = null;

        if (is_array($data)) foreach ($data as $key => $val)
        {
            if (!preg_match('/^'.$reg_exp.'/', $val, $matches))
            {
                $this->error = 'Незагруженные строки:<br />';
                $error_list .= 1+$key.' '.$val.'<br />';
            }
            else
            {
                $data_str = null;

                if (is_array($matches)) foreach ($matches as $k => $v)

                   if ($k > 0) $item_data[$data_descr[$k-1]] = trim($v);

                $parsed_data[] = $item_data;
            }
        }

        $this->error .= $error_list.'<hr />';

    return $parsed_data;
    }

    function preloader($parsed_data)
    {
        $categories_data = $this->get_categories_data();
        $incorrect_code  = null;

        if (is_array($parsed_data)) foreach ($parsed_data as $k => $v)
        {
            if (!$categories_data[$v['code']])

                $incorrect_code[$v['code']] = $v['code'];
            else
               $correct_code[$v['code']] = $categories_data[$v['code']]['id'];
        }

    return  array('correct' => $correct_code, 'incorrect' => $incorrect_code);
    }

    function get_main_categories_list()
    {
        $q = $this->get_root_categories();
        while ($r = mysql_fetch_assoc($q))

            $list .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';

    return $list;
    }

    function get_preload_list($incorrect_codes, $errors, $start, $perpage = 10)
    {
        $main_cat_list .= $this->get_main_categories_list();

        $limit = $start*$perpage+$perpage;
        $start = $start*$perpage;

        $c = 1;
        $n =-1;
        foreach ($incorrect_codes as $k => $v)
        {
            $n++;
            if ($n >= $start && $n < $limit)

            $preload_data .= '<tr '.(($c++%2 == 0)?'style="background: #F1F7FC"':'').'>
                                <td><input name="code[]" type="hidden" value="'.$v.'">'.$v.'</td>
                                <td><select size="1" name="root_category[]">
                                      <option value=""> --- </option>
                                 '.$main_cat_list.'
                                    </select>
                                 </td>
                                 <td><input name="cat_name[]" type="text" value=""></td>
                                 <td id="errors">'.$this->error_processing($v,$errors).'</td>
                              </tr>';
        }

        $html .= '<table width="80%" border="0" cellpadding="0" cellspacing="0">
                     <tr><th align="left">Код номенклатуры:</th>
                         <th align="left">Корневая категория:</th>
                         <th align="left">Имя категории:</th>
                         <th></th>
                     </tr>
                 '.$preload_data.'
                 <tr><td colspan="3"></td>
                     <td  align="right">
                       <input type="submit" value="Добавить">
                       <input type="button" value="Пропустить" onClick="javascript: document.location.href=\'?action=final_load\'">
                     </td></tr>
                  </table>';
    return $html;
    }

/*  Постраничный вывод
*/
    function preload_listing($incorrect_codes, $start, $perpage = 10)
    {
        $total = count($incorrect_codes);
        $page_count = (($total%$perpage == 0)?(int)($total/$perpage)-1:(int)($total/$perpage));

        for ($i = 0; $i < $page_count+1; $i++ )
        {
            if ($i == $start) $listing .= '<b style="font-size: 22px;">';

            $listing .= '<a href="?action=load&start='.$i.'">'.($i+1).'</a>&nbsp;';

            if ($i == $start) $listing .= '</b>';
        }

    return $listing;
    }

/*  Загрузка товаров
*/
    function load_products($parset_data, $incorrect_codes, $correct_categories)
    {
        $sort_orders = $this->define_product_sort_order($correct_categories);

        $error = '';
        $this->counter = 0;
        if (is_array($parset_data)) foreach ($parset_data as $k => $v)
        {
            if (empty($incorrect_codes[$v['code']]))
            {
                $sort_orders[$correct_categories[$v['code']]] = $sort_orders[$correct_categories[$v['code']]]+1;

                $sql1 = "INSERT INTO ".TBL_PREF."catalog_products SET
                        ".$this->get_set($v).
                        ",created = NOW(),
                         modified = NOW(),
                         published = '1',
                         sort_order = '".$sort_orders[$correct_categories[$v['code']]]."',
                         title = '".$v['name']."',
                         description = '".$v['name']."',
                         keywords = '".$v['name']."',
                         id_user = '1'";

                $this->db->Execute($sql1);

                $sql2 = "INSERT INTO ".TBL_PREF."catalog_p2c
                         SET id_product = '".mysql_insert_id()."',
                             id_category='".$correct_categories[$v['code']]."'";

                $this->db->Execute($sql2);

                $this->counter++;
            }
            else
            {
                $error = 'Коды номенклатуры не связанные с категориями:<br /> ';
                $error_list[$v['code']] = $v['code'];
            }
        }
    return $error.implode(', ',$error_list).'.';
    }

/*  Удаление старых категорий
*/
    function delete_old_products($categories_arr)
    {
        if (is_array($categories_arr))
        {
            $cat_set = implode(',',$categories_arr);

            $sql1 = "DELETE FROM ".TBL_PREF."catalog_products
                     WHERE id IN (

                         SELECT id_product
                         FROM ".TBL_PREF."catalog_p2c
                         WHERE id_category IN (".$cat_set."))";

            $sql2 = "DELETE FROM ".TBL_PREF."catalog_p2c WHERE id_category IN (".$cat_set.")";

            $this->db->Execute($sql1);
            $this->db->Execute($sql2);
        }
    }

/*  удаление всех продуктов
*/
    function total_products_delete()
    {
        $sql1 = "TRUNCATE TABLE ".TBL_PREF."catalog_products";
        $sql2 = "TRUNCATE TABLE ".TBL_PREF."catalog_p2c";

        $this->db->Execute($sql1);
        $this->db->Execute($sql2);
    }

    function insert_into_base($categories_name, $root_categories, $code, &$incorrect_codes, &$correct_categories)
    {
        $errors = null;
        $sort_orders = $this->define_sort_order();
        $names = $this->define_names();

        foreach ($code as $k => $v)
        {
            if (!empty($categories_name[$k]) && !empty($root_categories[$k]) &&
                 empty($names[$root_categories[$k]][$categories_name[$k]]))
            {
                $sort_orders[$root_categories[$k]] = ($sort_orders[$root_categories[$k]]+1);

                $sql = "INSERT INTO ".TBL_PREF."catalog_categories
                        SET id_parent = '".$root_categories[$k]."',
                            name = '".$categories_name[$k]."',
                            created = NOW(),
                            modified = NOW(),
                            published = '1',
                            sort_order = '".$sort_orders[$root_categories[$k]]."',
                            title = '".$categories_name[$k]."',
                            description = '".$categories_name[$k]."',
                            keywords = '".$categories_name[$k]."',
                            id_user = '1',
                            code = '".$v."'
                       ";

                $this->db->Execute($sql);

                if ( $cat_id = mysql_insert_id() )
                {
                    $correct_categories[$v] = $cat_id;
                    unset($incorrect_codes[$v]);
                }
            }
            else
                $errors[$v] = $this->error_definer($categories_name[$k], $root_categories[$k]);
        }

    return $errors;
    }

    private function get_set($array)
    {
        $set = '';
        if (is_array($array)) foreach ($array as $k => $v)

            if ($k != 'code') $set .= ','.$k.'="'.$this->prepare_decimal($v).'"';

    return substr($set,1);
    }

    private function prepare_decimal($value)
    {
        if (preg_match('/^\d+,{1}\d+/',$value))

            return str_replace(',','.',$value);
        else
            return $value;
    }

    private function error_definer($categories_name, $root_categories)
    {

        $errors = array('empty_name' => '', 'empty_root' => '', 'name_repeat' => '');

        if (empty($categories_name))
            $errors['empty_name'] = 'Введите имя категории.';
        elseif (empty($root_categories))
            $errors['empty_root'] = 'Выберите корневую категорию.';
        else
            $errors['name_repeat'] = 'Такая категория уже есть в базе.';

    return $errors;
    }

    private function error_processing($code, $error_data)
    {
        $error_mess = '';

        if (is_array($error_data[$code]))
        {
            foreach ($error_data[$code] as $k => $v)

                if (!empty($v)) $error_mess .= $v.'<br />';
        }
    return $error_mess;
    }

    private function define_sort_order()
    {
        $q = $this->get_root_categories();

        $sort_orders = null;

        while ($r = mysql_fetch_assoc($q))
        {
            $sql = "SELECT MAX(sort_order) as maxsort
                    FROM ".TBL_PREF."catalog_categories
                    WHERE id_parent = '".$r['id']."'";

            $max = $this->auth->QueryExecute($sql,0);

            $sort_orders[$r['id']] = $r['maxsort'];

        }

    return $sort_orders;
    }

    private function define_product_sort_order($categories)
    {
        $sql = "SELECT MAX(p.sort_order) as maxsort, p2c.id_category
                FROM ".TBL_PREF."catalog_products p
                LEFT JOIN ".TBL_PREF."catalog_p2c p2c ON p.id = p2c.id_product
                WHERE p2c.id_category IN (".implode($categories).")";

        $q = $this->db->Execute($sql);

        while ($r = mysql_fetch_assoc($q)) $sort[$r['id_category']] = $r['maxsort'];

    return $sort;
    }

    private function define_names()
    {
        $q = $this->get_root_categories();

        $names = null;
        while ($r = mysql_fetch_assoc($q))
        {
            $sql = "SELECT name
                    FROM ".TBL_PREF."catalog_categories
                    WHERE id_parent = '".$r['id']."'";

            $q = $this->db->Execute($sql);
            while ($rs = mysql_fetch_assoc($q))

               $names[$r['id']][$rs['name']] = $rs['name'];
        }

    return $names;
    }

    private function read_file( $file )
    {
        $data = null;

        if ($r = fopen($file,"r"))
        {
            $data = file($file);
            fclose($r);
        }
        else
            $this->error = 'Не удалось открыть файл';

    return  $data;
    }

    private function reg_exp()
    {
    return
    preg_replace('/\s+/','',
                 '(\d+);                              (?# код номенклатуры )
                  ([A-Za-zА-Яа-я\d\s\-,\.\/\(\)]+);   (?# наименование )
                  ([\d\.,]+);                         (?# I  )
                  ([\d\.,]+);                         (?# Q )
                  ([\d\.,]+);                         (?# U )
                  ([a-я\s\d\.,]*)\s*;                 (?# R )
                  ([\d\.,]+);                         (?# A )
                  ([\d\.,]+);                         (?# B )
                  ([\d\.,]+);                         (?# C )
                  ([\d\.,]+);                         (?# D )
                  ([\d\.,]+);                         (?# H )
                  ([\d\.,]+);                         (?# D )
                  ([\d\.,]+);                         (?# d )
                  ([А-ЯЁа-яё\s\d]*)\.*;               (?# герм )
                  ([\d\.,]+);                         (?# HT )
                  ([\d\.,]+);                         (?# Количество )
                  ([\d\.,]+)                          (?# Цена )');
    }

    private function data_description()
    {
    return array('code','name','I_max','Q_max','U','R','A','B','C','D','H','Drus','di','sealing_type','HT','quant','price');
    }

    private function get_categories_data()
    {
        $data = null;

        $sql = "SELECT *
                FROM ".TBL_PREF."catalog_categories
                WHERE id_parent<>'0'
                GROUP BY code
                ORDER BY code";
        $q = $this->db->Execute($sql);

        while ($r = mysql_fetch_assoc($q)) $data[$r['code']] = $r;

    return $data;
    }

    private function get_root_categories()
    {
        $sql = "SELECT *
                FROM ".TBL_PREF."catalog_categories
                WHERE id_parent = '0'";

    return $this->db->Execute($sql);
    }

}

?>